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

use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeProject;
use Goteo\Model\Node\NodeFaq;
use Goteo\Model\Node\NodeFaqQuestion;
use Goteo\Model\Node\NodeFaqDownload;
use Goteo\Model\Node\NodeResource;
use Goteo\Model\Node\NodeResourceCategory;
use Goteo\Model\Home;
use Goteo\Model\Project;
use Goteo\Model\Sponsor;
use Goteo\Model\Category;
use Goteo\Library\Text;
use Goteo\Model\Page;
use Goteo\Model\SocialCommitment;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Questionnaire;


class ChannelController extends \Goteo\Core\Controller {
    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);

        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    // Set the global vars to all the channel views
    private function setChannelContext($id)
    {

        try {
            $channel = Node::get($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }

        //Check if the user can access to the channel
        $user = Session::getUser();

        if(!$channel->active && (!$user || !$channel->userCanView($user)))
            throw new ControllerAccessDeniedException("You don't have permissions to access to this channel!");

        $categories = Category::getNames();

        $side_order = Home::getAll($id, 'side'); // side order

        $types = (!$channel->premium) ? array(
            'popular',
            'recent',
            'success',
            'outdate'
        ): [];

        $config = $channel->getConfig();

        if($config['google_analytics']) {
                Config::set('analytics.google', array_merge(Config::get('analytics.google'), [$config['google_analytics']]));
            }

        // get custom colors from config field
        $colors=$config['colors'] ? $config['colors'] : [];

        //check if there are elements to show by type
        foreach($types as $key => $type)
        {
            $total = Project::published(['type' => $type], $id, 0, 0, true);
            if(!$total&&$type!='popular')
                unset($types[$key]);
        }

        if (isset($side_order['summary'])) {
            $summary = $channel->getSummary();
        }

        if (isset($side_order['sponsors'])) {
            // Patrocinadores del nodo
            $sponsors = Sponsor::getList(['node' => $id]);
        }

        $this->contextVars([
            'channel'     => $channel,
            'side_order' => $side_order,
            'summary' => $summary,
            'sumcalls' => $sumcalls,
            'sponsors' => $sponsors,
            'categories' => $categories,
            'types' => $types,
            'colors' => $colors,
            'url_project_create' => '/channel/' . $id . '/create'
        ], 'channel/');
    }

    /**
     * @param  [type] $id   Channel id
     */
    public function indexAction($id, Request $request)
    {
        $this->setChannelContext($id);

        // Proyectos destacados si hay

        $limit = 999;

        $channel = Node::get($id);
        $config = $channel->getConfig();

        if ($config['projects']) {
            $list = Project::getList($config['projects'], $id, 0, $limit);
        } else if($list = Project::published(['type' => 'promoted'], $id, 0, $limit)) {
            $total = count($list);
        }
        else {
            // if no promotes let's show some random projects...
            $limit = 9;
            $total = $limit;
            $list = Project::published(['type' => 'random'], $id, 0, $limit);
        }

        $view= $channel->type=='normal' ? 'channel/list_projects' : 'channel/'.$channel->type.'/index';

        return $this->viewResponse(
            $view,
            [
                'projects' => $list,
                'category'=> $category,
                'title_text' => Text::get('node-side-searcher-promote'),
                'type' => $type,
                'total' => $total,
                'limit' => $limit,
                'map' => $map
            ]
        );
    }

    /**
     * All channel projects
     * @param  [type] $id   Channel id
     * @param  Request $request [description]
     */
    public function listProjectsAction($id, $type = 'available', $category = null, Request $request)
    {
        $this->setChannelContext($id);

        $channel = Node::get($id);

        $limit = 9;
        $status=[3,4,5];
        $filter = ['type' => $type, 'popularity' => 5, 'status' => $status ];

        $title_text = $type === 'available' ? Text::get('regular-see_all') : Text::get('node-side-searcher-'.$type);
        if($category) {
            if($cat = Category::get($category)) {
                $title_text .= ' / '. Text::get('discover-searcher-bycategory-header') . ' ' . $cat->name;
                $filter['category'] = $category;
            }
        }

        $list = Project::published($filter, $id, (int)$request->query->get('pag') * $limit, $limit);
        $total = Project::published($filter, $id, 0, 0, true);

        $view= $channel->type=='normal' ? 'channel/list_projects' : 'channel/'.$channel->type.'/list_projects';

        return $this->viewResponse(
            $view,
                [
                'projects' => $list,
                'category'=> $category,
                'title_text' => $title_text,
                'type' => $type,
                'total' => $total,
                'limit' => $limit
                ]
        );
    }

    /**
     * Channel terms
     * @param  Request $request [description]
     */
    public function faqAction ($id, $slug, Request $request)
    {
        $this->setChannelContext($id);

        $faq= NodeFaq::getBySlug($id, $slug);

        $questions=NodeFaqQuestion::getList(['node_faq' => $faq->id]);

        $downloads=NodeFaqDownload::getList(['node_faq' => $faq->id]);

        return $this->viewResponse('channel/call/faq',
            ['faq' => $faq,
             'questions' => $questions,
             'downloads' => $downloads
            ]
        );
    }

     /**
     * Channel resorces pagee
     * @param  Request $request [description]
     */
    public function resourcesAction ($id, $slug='', Request $request)
    {
        $this->setChannelContext($id);

        if($slug)
            $category_id=NodeResourceCategory::getIdBySlug($slug);

        $resources=NodeResource::getList(['node' => $id, 'category' => $category_id]);

        $resources_categories=NodeResourceCategory::getlist();

        return $this->viewResponse('channel/call/resources',
            [
            'resources' => $resources,
            'category'  => $category_id,
            'resources_categories' => $resources_categories
            ]
        );
    }


    /**
     * Initial create project action
     * @param  Request $request [description]
     */
    public function createAction ($id, Request $request)
    {
        // Some context vars for compatibility (to be removed)
        $this->contextVars(['url_project_create' => '/channel/' . $id . '/create']);

        if (!Session::isLogged()) {
            Message::info(Text::get('user-login-required-to_create'));
            return $this->redirect('/user/login?return='.urldecode("/channel/$id/create"));
        }

        if($request->isMethod('post')) {
            // Re-use original post action:
            // The listener ProjectChannelListener does all the work on changing the assigned channel
            return $this->forward('Goteo\Controller\ProjectController::createAction');
        }

        return $this->viewResponse( 'project/create', [
            'social_commitments' => SocialCommitment::getAll(),
            'terms'      => Page::get('howto'),
            'project_defaults' => ['node' => $id]
        ]);
    }

    /**
     * Initial apply project action
     * @param  Request $request [description]
     */

    public function applyAction ($id, $pid, Request $request)
    {
        if (!Session::isLogged()) {
            Message::info(Text::get('user-login-required-to_create'));
            return $this->redirect('/user/login?return='.urldecode("/channel/$id/apply/$pid"));
        }

        $channel = Node::get($id);
        $project = Project::get($pid);
        if(!$project->inEdition()) {
            Message::error('Project must be in edition to assign to a call');
            return $this->redirect("/dashboard/project/$pid");
        }

        if (!NodeProject::getList(['node' => $id, 'project' => $pid])) {
            $node_project = new NodeProject();
            $node_project->node_id = $id;
            $node_project->project_id = $pid;
            $errors = array();
            $node_project->save($errors);
            if ($errors) {
                Message::error(implode(',', $errors));
                return $this->redirect($request->headers->get('referer'));
            }
        }

        $questionnaire = Questionnaire::getByMatcher($id);
        if (!$questionnaire)
            $questionnaire = Questionnaire::getByChannel($id);

        if ($questionnaire->isAnswered($pid)) {
            Message::error(Text::get('questionnaire-already-answered-by-project'));
            return $this->redirect("/dashboard/project/$pid");
        }

        if ($questionnaire->questions) {
            $questionnaire->project_id = $pid;
            $processor = $this->getModelForm('Questionnaire', $questionnaire, (array) $questionnaire, [], $request);
            $processor->createForm()->getBuilder()
                ->add(
                    'submit', SubmitType::class, [
                        'label' => 'regular-submit',
                        'attr' => ['class' => 'btn btn-lg btn-cyan text-uppercase'],
                        'icon_class' => 'fa fa-save'
                    ]
                );

            $form = $processor->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $request->isMethod('post')) {
                // Check if we want to remove an entry

                try {
                    $processor->save($form); // Allow save event if does not validate
                    // Message::info(Text::get(''));
                    return $this->redirect('/dashboard/project/' . $project->id . '/profile');
                } catch (FormModelException $e) {
                    Message::error($e->getMessage());
                }
            }

            return $this->viewResponse(
                'questionnaire/apply',
                [
                    'model' => $channel,
                    'form' => $form->createView()
                ]
            );
        }

        return $this->redirect('/dashboard/project/'. $project->id .'/profile');
    }

     /**
     * List of channels
     * @param  Request $request [description]
     */
    public function listChannelsAction (Request $request)
    {
        $channels=Node::getAll(['status' => 'active', 'type' => 'channel']);

        foreach ($channels as $chanelId => $channel) {
            if(!$channel->home_img)
                unset($channels[$chanelId]);
        }

        return $this->viewResponse('channels/list', ['channels' => $channels]);
    }

        /**
    * Returns an array suitable for Project::getList($filters)
     */
    protected function getProjectFilters($filter, $vars = []) {
        $filters = $ofilters = [
            'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED],
            'published_since' => (new \DateTime('-6 month'))->format('Y-m-d')
        ];

        $filters['order'] = 'project.status ASC, project.published DESC, project.name ASC';
        if($vars['q']) {
            $filters['global'] = $vars['q'];
            unset($filters['published_since']);
            $filters['status'] = [ Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
        }
        elseif($vars['category']) {
            $filters['category'] = $vars['category'];
            unset($filters['published_since']);
            $filters['status'] = [ Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
        }
        elseif($vars['location'] || ($vars['latitude'] && $vars['longitude'])) {
            // $filters['location'] = $vars['location'];
            unset($filters['published_since']);
            $filters['location'] = new ProjectLocation([ 'location' => $vars['location'], 'latitude' => $vars['latitude'], 'longitude' => $vars['longitude'], 'radius' => 300 ]);
            $filters['status'] = [ Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
            $filters['order'] = 'Distance ASC, project.status ASC, project.published DESC, project.name ASC';
        }
        elseif($filter === 'near') {
            // Nearby defined as 300Km distance
            // Any LocationInterface will do (UserLocation, ProjectLocation, ...)
            $filters['location'] = new ProjectLocation([ 'latitude' => $vars['latitude'], 'longitude' => $vars['longitude'], 'radius' => 300 ]);
            $filters['order'] = 'Distance ASC, project.status ASC, project.published DESC, project.name ASC';
        } elseif($filter === 'outdated') {
            $filters['type'] = 'outdated';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'project.days ASC, project.published DESC, project.name ASC';
        } elseif($filter === 'promoted') {
            $filters['type'] = 'promoted';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'promote.order ASC, project.published DESC, project.name ASC';
        } elseif($filter === 'popular') {
            $filters['type'] = 'popular';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'project.popularity DESC, project.published DESC, project.name ASC';
        } elseif($filter === 'succeeded') {
            $filters['type'] = 'succeeded';
            $filters['status'] = [Project::STATUS_FUNDED, Project::STATUS_FULFILLED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            // $filters['published_since'] = (new \DateTime('-12 month'))->format('Y-m-d');
            unset($filters['published_since']);
        } elseif($filter === 'fulfilled') {
            $filters['status'] = [Project::STATUS_FULFILLED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            // $filters['published_since'] = (new \DateTime('-24 month'))->format('Y-m-d');
            unset($filters['published_since']);
        } elseif($filter === 'archived') {
            $filters['status'] = [Project::STATUS_UNFUNDED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            $filters['published_since'] = (new \DateTime('-24 month'))->format('Y-m-d');
        } elseif($filter === 'matchfunding') {
            $filters['type'] = 'matchfunding';
            // $filters['published_since'] = (new \DateTime('-24 month'))->format('Y-m-d');
            unset($filters['published_since']);
        } elseif($filter === 'recent') {
            $filters['type'] = 'recent';
        }

        if($vars['review']) {
            $filters['status'] = [ Project::STATUS_EDITING, Project::STATUS_REVIEWING, Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
            $filters['is_draft'] = true;
        }
        return $filters;
    }

    /*
    * Discover projects, general page
    */
    public function discoverProjectsAction($id = null, $filter = '', Request $request)
    {
        try {
            $this->setChannelContext($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/');
        }

        $channel = Node::get($id);

        $limit = 12;
        $q = strip_tags($request->query->get('q'));
        $location = strip_tags($request->query->get('location'));
        $latitude = strip_tags($request->query->get('latitude'));
        $longitude = strip_tags($request->query->get('longitude'));
        $category = $request->query->get('category');
        $vars = ['q' => $q, 'category' => $category, 'location' => $location, 'latitude' => $latitude, 'longitude' => $longitude];
        if(Session::isAdmin()) {
            $vars['review'] = $request->query->get('review') === '1' ? 1 : 0 ;
        }

        $filters = $this->getProjectFilters($filter, $vars);
        $filters['node'] = $id;

        $projects = Project::getList($filters, null, 0, $limit);
        $total = Project::getList($filters, null, 0, 0, true);

        return $this->viewResponse('channel/results', [
            'channel' => $channel,
            'projects' => $projects,
            'categories' => Category::getNames(),
            'filter' => $filter,
            'total' => $total,
            'discover_module' => true,
            'limit' => $limit
        ]);
    }

    /**
     * Ajax projects search
     */
    public function ajaxSearchAction($id = null, Request $request) {

        try {
            Node::get($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return;
        }

        $limit = $request->get('limit', 24); // extracts from GET, PATH or POST
        $pag = $request->get('pag', 0);
        $limit = max(1, min(25, abs($limit)));
        $pag = max(0, abs($pag));
        $filter = $request->get('filter');
        $q = strip_tags($request->get('q'));
        $location = strip_tags($request->get('location'));
        $latitude = strip_tags($request->get('latitude'));
        $longitude = strip_tags($request->get('longitude'));
        $category = $request->get('category');

        $ofilters = [
            'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED],
            'published_since' => (new \DateTime('-6 month'))->format('Y-m-d')
        ];
        $filters = $this->getProjectFilters($filter, ['q' => $q, 'category' => $category, 'location' => $location, 'latitude' => $latitude, 'longitude' => $longitude]);
        $filters['node'] = $id;

        $offset = $pag * $limit;
        $total_projects = 0;
        $projects = Project::getList($filters, null, $offset, $limit);
        if($projects) {
            $total_projects = Project::getList($filters, null, 0, 0, true);
        } elseif(!$request->query->has('strict')) {
            // Home controller does not send 'strict' query string, we always want projects in home:
            $projects = Project::getList($ofilters, null, $offset, $limit);
            $total_projects = Project::getList($ofilters, null, 0, 0, true);
        }

        $vars = [
            'filter' => $filter,
            'limit' => $limit,
            'pag' => $pag,
            'total' => $total_projects,
            'items' => []
        ];
        foreach($projects as $p) {
            $vars['items'][] = View::render('project/widgets/normal', ['project' => $p]);
        }
        return $this->jsonResponse($vars);
    }


}
