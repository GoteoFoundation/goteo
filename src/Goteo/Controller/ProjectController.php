<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use DateTime;
use Goteo\Application\AppEvents;
use Goteo\Application\Config;
use Goteo\Application\Event\FilterProjectEvent;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Controller\Dashboard\ProjectDashboardController;
use Goteo\Core\Controller;
use Goteo\Core\DB;
use Goteo\Library\Text;
use Goteo\Library\Worth;
use Goteo\Model\Blog;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Model\Footprint;
use Goteo\Model\ImpactData;
use Goteo\Model\ImpactData\ImpactDataProject;
use Goteo\Model\ImpactItem\ImpactProjectItem;
use Goteo\Model\Invest;
use Goteo\Model\License;
use Goteo\Model\Message as SupportMessage;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeProject;
use Goteo\Model\Page;
use Goteo\Model\Project;
use Goteo\Model\Project\Account;
use Goteo\Model\Project\Category;
use Goteo\Model\Project\Favourite;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Project\ProjectMilestone;
use Goteo\Model\SocialCommitment;
use Goteo\Repository\AnnouncementRepository;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller {

    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

	public function indexAction(Request $request, $pid = null, $show = 'home', $post = null) {
		if ($pid !== null) {
			return $this->view($request, $pid, $show, $post);
		}
		if ($request->query->has('create')) {
			return new RedirectResponse('/project/create');
		}
		return new RedirectResponse('/discover');
	}

	public function createAction(Request $request) {
        if (!Session::isLogged()) {
            Message::info(Text::get('user-login-required-to_create'));
            return $this->redirect('/user/login?return='.urldecode('/project/create'));
        }

        if ($request->isMethod(Request::METHOD_POST)) {

            $redirectToCalculator = $request->request->getBoolean('continue-impact', false);
        	$social_commitment= strip_tags($request->request->get('social'));

            $data=[
                'name' => strip_tags($request->request->get('name')),
                'subtitle' => strip_tags($request->request->get('subtitle')),
                'social_commitment' => $social_commitment,
                'social_description' => strip_tags($request->request->get('social-description')),
                'location' => $request->request->get('location'),
                'project_location' => $request->request->get('project_location')
            ];

            $project = Project::createNewProject($data);
        	$categories = SocialCommitment::getCategories($social_commitment);

        	foreach ($categories as $item) {
        		$category=new Category();
        		$category->project=$project->id;
        		$category->id=$item;
        		$category->save();
        	}

        	$projectLocation = new ProjectLocation([
                'id' => $project->id,
                'city' => $request->request->get('city'),
                'region' => $request->request->get('region'),
                'country' => $request->request->get('country'),
                'country_code' => $request->request->get('country_code'),
                'longitude' => $request->request->get('longitude'),
                'latitude' => $request->request->get('latitude'),
                'method' => 'manual'
            ]);

            $projectLocation->save($errors);

            // Save publishing day and min required estimation
            $conf = Project\Conf::get($project->id);
            $conf->mincost_estimation = $request->request->get('minimum');
            $conf->publishing_estimation = $request->request->get('publishing_date');
            $conf->save();

            // Save default fee
            $accounts = new Account();
            $accounts->project = $project->id;
            $accounts->allowpp = false;
            $accounts->fee = Config::get('fee');
            $accounts->save();

            $response = $this->dispatch(AppEvents::PROJECT_CREATED, new FilterProjectEvent($project))->getResponse();
            if($response instanceOf Response) return $response;

            if ($redirectToCalculator)
                return new RedirectResponse("/project/$project->id/impact-calculator");

            return new RedirectResponse("/dashboard/project/$project->id");
        }

        return $this->viewResponse( 'project/create', [
           'social_commitments' => SocialCommitment::getAll(),
           'terms' => Page::get('howto')
        ]);
    }

	protected function view(Request $request, $project, $show, $post = null) {
		DB::cache(true);

        $announcementRepository = new AnnouncementRepository();
        $announcementList = $announcementRepository->getActiveWithoutDonationList();

		if( !$project instanceOf Project ) {
            $project = Project::get($project, Lang::current(false));
        }
		$user = Session::getUser();
		$show_allow=['home', 'updates', 'participate'];

		if(!in_array($show, $show_allow))
			return $this->redirect('/project/' . $project->id);

        if ($project->node != Config::get('node'))
            $related_projects = Project::published([], $project->node, 0, 3);
        else
            $related_projects = Project::published(['categories' => $project->categories], null, 0, 3);

		$lsuf = (LANG != 'es') ? '?lang='.LANG : '';
        $URL = '//'.$request->getHttpHost();
        $url = $URL . '/widget/project/' . $project->id;
        $widget_code = Text::widget($url . $lsuf);
        $type = $project->type;

        // mensaje cuando, sin estar en campaña, tiene fecha de publicación
        if (!$project->isApproved()) {
            if (!empty($project->published)) {
                if ($project->published >= date('Y-m-d')) {
                    Message::info(Text::get('project-willpublish', date('d/m/Y', strtotime($project->published))));
                } else {
                    Message::info(Text::get('project-unpublished'));
                }
            } else {
                Message::info(Text::get('project-not_published'));
            }
        }

        if ($project->userCanView(Session::getUser())) {
            ProjectDashboardController::createProjectSidebar($project, 'preview');

            $project->cat_names = Project\Category::getNames($project->id);

            if ($show == 'home') {
                // para el widget embed
                $project->rewards = array_merge($project->social_rewards, $project->individual_rewards);
            }

            // TODO: do the same with facebook pixel (not done yet because f.pixel is only used in the project page)
            if($project->analytics_id) {
                Config::set('analytics.google', array_merge(Config::get('analytics.google'), [$project->analytics_id]));
            }

            $footprints = Footprint::getList([], 0, 3);

            $viewData = [
                'project' => $project,
                'show' => $show,
                'blog' => null,
                'related_projects' => $related_projects,
                'widget_code' => $widget_code,
                'footprints' => $footprints,
                'announcements' => $announcementList
            ];

            $impactDataProjectByFootprint = [];
            foreach($footprints as $footprint) {
                $impactDataProjectByFootprint[$footprint->id] = ImpactDataProject::getCalculatedByProjectAndFootprint($project, $footprint);
            }

            $impactDataProjectList = ImpactDataProject::getCalculatedListByProject($project);

            $impactProjectItemList = [];
            foreach($impactDataProjectList as $impactDataProject) {
                $impactData = $impactDataProject->getImpactData();
                $impactProjectItemList[$impactData->id] = ImpactProjectItem::getListByProjectAndImpactData($project, $impactData);
            }

            $viewData['impactDataProjectByFootprint'] = $impactDataProjectByFootprint;
            $viewData['impactDataProjectList'] = $impactDataProjectList;
            $viewData['impactProjectItemList'] = $impactProjectItemList;

            $viewData['matchers'] = $project->getMatchers('active');
            $viewData['individual_rewards'] = [];

            $viewData['channels'] = $this->getChannelsForProject($project);

            foreach ($project->getIndividualRewards(Lang::current(false)) as $reward) {
                if ($reward->available() || !$project::hideExhaustedRewards($project->id) || !$project->inCampaign()) {
                    $reward->none  = false;
                    $reward->taken = $reward->getTaken();// cofinanciadores que han optado por esta recompensa
                    // si controla unidades de esta recompensa, mirar si quedan
                    if ($reward->units > 0 && $reward->taken >= $reward->units) {
                        $reward->none = true;
                    }
                    $viewData['individual_rewards'][] = $reward;
                }
            }

            // retornos adicionales (bonus)
            $viewData['bonus_rewards'] = [];
            $viewData['social_rewards'] = [];
            foreach ($project->getSocialRewards() as $reward) {
                if($reward->url && stripos($reward->url, 'http') !== 0) {
                    $reward->url = 'http://' .  $reward->url;
                }
                if ($reward->bonus) {
                    $viewData['bonus_rewards'][] = $reward;
                } else {
                    $viewData['social_rewards'][] = $reward;
                }
            }

            switch ($show) {
            case 'home':
                    $viewData['types'] = Project\Cost::types();
                    // Costs by type
                    $costs = array();

                    foreach ($project->costs as $cost) {
                        $costs[$cost->type][] = (object)array(
                            'name' => $cost->cost,
                            'description' => $cost->description,
                            'min' => $cost->required == 1 ? $cost->amount : '',
                            'opt' => $cost->amount,
                            'req' => $cost->required
                        );
                    }

                    $viewData['costs'] = $costs;
                    $licenses = array();

                    foreach (License::getAll() as $l) {
                        $licenses[$l->id] = $l;
                    }

                    $viewData['licenses'] = $licenses;
                    break;
                case 'needs-on':
                    $viewData['show'] = 'needs';
                    $viewData['non_economic'] = true;
                    break;
                case 'updates':
                    //if is an individual post page
                    if ($post) {
                        $pob = BlogPost::getBySlug($post, Lang::current(), $project->lang);
                        if($pob->slug && $post != $pob->slug) {
                            return $this->redirect("/project/{$project->id}/updates/{$pob->slug}");
                        }
                        $viewData['post']  = $pob;
                        $show  = 'updates_post';
                    }

                    // sus entradas de novedades
                    $blog = Blog::get($project->id);
                    $milestones = ProjectMilestone::getAll($project->id, Lang::current(), $project->lang);
                    $viewData['milestones']=$milestones;
                    $viewData['blog'] = $blog;
                    $viewData['owner'] = $project->owner;

                    if (empty($user)) {
                        Message::info(Text::html('user-login-required'));
                    }
                    break;
                case 'participate':
                    $viewData['worthcracy']=Worth::getAll();
                    $limit=15;
                    $pag = max(0, (int)$request->query->get('pag'));
                    $viewData['investors_list']= Invest::investors($project->id, false, false, $pag * $limit, $limit, false);
                    $viewData['investors_total'] = Invest::investors($project->id, false, false, 0, 0, true);
                    $viewData['investors_limit'] = $limit;

                    // Collaborations
                    $viewData['messages'] = SupportMessage::getAll($project->id, Lang::current());

                    if (empty($user)) {
                        Message::info(Text::html('user-login-required'));
                    }
                    break;
                case 'messages':
                    if ($project->status < 3)
                        Message::info(Text::get('project-messages-closed'));
                    break;
            }

            $response = new Response(View::render("project/$show", $viewData));
            if(!$project->isApproved()) {
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
                $response->headers->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
            }
            return $response;
        } else {
            Message::info('Project not public yet!');
            return new RedirectResponse('/');
        }
    }

    /**
     * A user mark a project as favourite
     * TODO: to microAPI
     */
    public function favouriteAction($pid, Request $request) {

        if (!Session::isLogged()) {
            return $this->redirect('/user/login?return='.urldecode('/project/favourite/'.$pid));
        }

        $user = Session::getUser()->id;

        //Calculate the date to send mail
        $project = Project::get($pid, Lang::current(false));

        if ( ($project->days>1) && ($project->round==1) && ($project->amount<$project->mincost) ) {
            $interval_days_send = round(($project->days-1)*0.8);
            $date_send = new DateTime(date('Y-m-d'));
            $date_send = $date_send->modify("+".$interval_days_send." days");
            $date_send = $date_send->format('Y-m-d');
        }

        $favourite=new Favourite([
            'project' => $pid,
            'user' => $user,
            'date_send' => $date_send
        ]);

        $favourite->save($errors);

        if ($request->isMethod(Request::METHOD_POST))
            return $this->jsonResponse(['result' => $favourite]);

        return $this->redirect('/project/' . $pid);
    }

    /**
     * A user unmark a project as favourite
     * TODO: to microAPI
     */
    public function deleteFavouriteAction(Request $request): JsonResponse
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $obj = json_decode($request->getContent());
            $project = $obj->project;
            $user = $obj->user;

            $favourite = new Favourite([
                'project' => $project, 'user' => $user
            ]);

            $errors = [];
            if (!$favourite->remove($errors))
                return $this->jsonResponse(['result' => implode(',',$errors)]);
        }

        return $this->jsonResponse(['result' => true]);
    }

    public function posterAction($pid) {
        $project=Project::get($pid, Lang::current(false));
        try {
            $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(5,0,5,8));
            $html2pdf->setTestTdInOnePage(false);
            $html2pdf->writeHTML(View::render('poster/project.php', ["project" => $project]));
            $html2pdf->pdf->SetTitle('Poster');
            $pdf = $html2pdf->output();
            $response = new Response($pdf);
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        } catch(Html2PdfException $e) {
            Message::error($e->getMessage());
            return new RedirectResponse('/project/' . $project->id );
        }
    }

    public function impactAction(Request $request, string $pid = null): Response
    {
        if (!Session::isLogged()) {
            Message::info(Text::get('user-login-required-to_create'));
            return $this->redirect('/user/login?return='.urldecode("/project/$pid/impact-calculator"));
        }

        $user = Session::getUser();
        $project = Project::get($pid);

        if (!$user->id == $project->user) {
            return $this->redirect('/');
        }

        $footprints = Footprint::getList([], 0, 3);

        if ($request->isMethod(Request::METHOD_POST)) {
            $this->createImpactDataProjects($request, $project);
            return new RedirectResponse('/dashboard/project/' . $project->id . '/profile');
        }

        return $this->viewResponse('project/impact_calculator/impact_calculator', ['footprints' => $footprints, 'project' => $project]);
    }

    private function createImpactDataProjects(Request $request, Project $project)
    {
        $data = $request->request->all();
        foreach ($data['form'] as $impactDataList) {
            foreach ($impactDataList as $impactData => $impactDataProjectData) {
                $impactData = ImpactData::get($impactData);

                if ($impactDataProjectData['active']  && !empty($impactDataProjectData["data"]) && !empty($impactDataProjectData["estimated_amount"])) {
                    $errors = [];
                    $this->createAndPersistImpactDataProject($impactData, $project, $impactDataProjectData, $errors);
                    if (!empty($errors)) {
                        Message::error($errors);
                    }
                }
            }
        }
    }

    public function createAndPersistImpactDataProject(ImpactData $impactData, Project $project, array $impactDataProjectData, array $errors = []): void
    {
        $impactDataProject = new ImpactDataProject();
        $impactDataProject
            ->setImpactData($impactData)
            ->setProject($project)
            ->setData($impactDataProjectData["data"])
            ->setEstimationAmount($impactDataProjectData["estimated_amount"]);

        $impactDataProject->save($errors);
    }

    /**
     * @return Node[]
     */
    private function getChannelsForProject(Project $project): array
    {
        $channels = [];

        $nodeProjectList = NodeProject::getList([
            'project' => $project->id
        ]);

        foreach($nodeProjectList as $node) {
            $channels[] = Node::get($node->node_id);
        }

        return $channels;
    }
}
