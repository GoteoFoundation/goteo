<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Dashboard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Goteo\Application\Session;
use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Application\Lang;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\Project\Account;
use Goteo\Model\Project\Cost;
use Goteo\Model\Project\Reward;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Project\Support;
use Goteo\Model\User;
use Goteo\Model\Blog;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Model\Message as Comment;
use Goteo\Library\Text;
use Goteo\Console\UsersSend;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Event\FilterMessageEvent;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Forms\FormModelException;
use Goteo\Application\Event\FilterProjectEvent;
use Goteo\Application\Event\FilterProjectPostEvent;
use Goteo\Controller\DashboardController;

class ProjectDashboardController extends DashboardController {
    protected $user, $admin = false;

    public function __construct() {
        parent::__construct();

        $this->contextVars([
            'section' => 'projects'
        ]);
    }

    static function createProjectSidebar(Project $project, $zone = '', &$form = null) {
        $user = Session::getUser();

        if(!$project->userCanEdit($user)) return false;
        $prefix = '/dashboard/project/' . $project->id ;

        // Create sidebar menu
        Session::addToSidebarMenu('<i class="icon icon-2x icon-summary"></i> ' . Text::get('dashboard-menu-activity-summary'), $prefix . '/summary', 'summary');

        $validation = false;
        $admin = false;
        $validation = $project->getValidation();
        $admin = $project->userCanModerate($user) && !$project->inEdition();


        if($project->inEdition() || $admin) {
            $steps = [
                ['text' => '<i class="icon icon-2x icon-user"></i> 1. ' . Text::get('profile-about-header'), 'link' => $prefix . '/profile', 'id' => 'profile', 'class' => $validation->profile == 100 ? 'ok' : 'ko'],
                // ['text' => '<i class="fa fa-2x fa-id-card-o"></i> 2. ' . Text::get('step-2'), 'link' => $prefix . '/personal', 'id' => 'personal'],
                ['text' => '<i class="icon icon-2x icon-edit"></i> 2. ' . Text::get('step-3'), 'link' => $prefix . '/overview', 'id' => 'overview', 'class' => $validation->overview == 100 ? 'ok' : 'ko'],
                ['text' => '<i class="icon icon-2x icon-images"></i> 3. ' . Text::get('step-3b'), 'link' => $prefix . '/images', 'id' => 'images', 'class' => $validation->images == 100 ? 'ok' : 'ko'],
                ['text' => '<i class="fa fa-2x fa-tasks"></i> 4. ' . Text::get('step-4'), 'link' => $prefix . '/costs', 'id' => 'costs', 'class' => $validation->costs == 100 ? 'ok' : 'ko'],
                ['text' => '<i class="fa fa-2x fa-gift"></i> 5. ' . Text::get('step-5'), 'link' => $prefix . '/rewards', 'id' => 'rewards', 'class' => $validation->rewards == 100 ? 'ok' : 'ko'],
                ['text' => '<i class="fa fa-2x fa-sliders"></i> 6. ' . Text::get('project-campaign'), 'link' => $prefix . '/campaign', 'id' => 'campaign', 'class' => $validation->campaign == 100 ? 'ok' : 'ko'],
                ['text' => '<i class="icon icon-2x icon-supports"></i> ' . Text::get('dashboard-menu-projects-supports'), 'link' => $prefix . '/supports', 'id' => 'supports'],
            ];
            Session::addToSidebarMenu('<i class="icon icon-2x icon-projects"></i> ' . Text::get('project-edit'), $steps, 'project', null, 'sidebar' . ($admin ? ' admin' : ''));
        }

        if($project->isApproved()) {
            $submenu = [
                // ['text' => '<i class="icon icon-2x icon-updates"></i> ' . Text::get('dashboard-menu-projects-updates'), 'link' => $prefix . '/updates', 'id' => 'updates'],
                ['text' => '<i class="icon icon-2x icon-updates"></i> ' . Text::get('regular-header-blog'), 'link' => $prefix . '/updates', 'id' => 'updates'],
                ['text' => '<i class="icon icon-2x icon-donors"></i> ' . Text::get('dashboard-menu-projects-rewards'), 'link' => $prefix . '/invests', 'id' => 'invests'],
                ['text' => '<i class="icon icon-2x icon-images"></i> ' . Text::get('step-3b'), 'link' => $prefix . '/images', 'id' => 'images'],
                ['text' => '<i class="fa fa-2x fa-gift"></i> ' . Text::get('step-5'), 'link' => $prefix . '/rewards', 'id' => 'rewards'],
                ['text' => '<i class="icon icon-2x icon-supports"></i> ' . Text::get('dashboard-menu-projects-supports'), 'link' => $prefix . '/supports', 'id' => 'supports'],
            ];
            // Session::addToSidebarMenu('<i class="icon icon-2x icon-partners"></i> ' . Text::get('dashboard-menu-projects-messegers'), '/dashboard/projects/messengers/select?project=' . $project->id, 'comments');
            Session::addToSidebarMenu('<i class="icon icon-2x icon-projects"></i> ' . Text::get('project-manage-campaign'), $submenu, 'project', null, 'sidebar');
        }


         $submenu = [
            ['text' => '<i class="fa fa-2x fa-globe"></i> ' . Text::get('regular-translations'), 'link' => $prefix . '/translate', 'id' => 'translate'],
            ['text' => '<i class="icon icon-2x icon-analytics"></i> ' . Text::get('dashboard-menu-projects-analytics'), 'link' => $prefix . '/analytics', 'id' => 'analytics'],
            ['text' => '<i class="icon icon-2x icon-shared"></i> ' . Text::get('project-share-materials'), 'link' => $prefix . '/materials', 'id' => 'materials']
        ];

        Session::addToSidebarMenu('<i class="icon icon-2x icon-settings"></i> ' . Text::get('footer-header-resources'), $submenu, 'resources', null, 'sidebar');

        Session::addToSidebarMenu('<i class="icon icon-2x icon-preview"></i> ' . Text::get($project->isApproved() ? 'dashboard-menu-projects-preview' : 'regular-preview' ), '/project/' . $project->id, 'preview');

        if($project->inEdition() && $validation->global == 100) {

            Session::addToSidebarMenu('<i class="fa fa-2x fa-paper-plane"></i> ' . Text::get('project-send-review'), '/dashboard/project/' . $project->id . '/apply', 'apply', null, 'flat', 'btn btn-fashion apply-project');

        }

        // Create a global form to send to review
        $builder = App::getService('app.forms')->createBuilder(
            [ 'message' => $project->comment ],
            'applyform',
            [
              'action' => '/dashboard/project/' . $project->id . '/apply',
              'attr' => ['class' => 'autoform']
            ]);

        $form = $builder
            ->add('message', 'textarea', [
                'label' => 'preview-send-comment',
                'required' => false,
                // 'attr' => ['help' => Text::get('tooltip-project-support-description')]
            ])
            ->add('submit', 'submit', [
                'label' => 'project-send-review',
                'attr' => ['class' => 'btn btn-fashion btn-lg'],
                'icon_class' => 'fa fa-paper-plane'
            ])
            ->getForm();

        View::getEngine()->useData([
            'applyForm' => $form->createView(),
            'project' => $project,
            'validation' => $validation,
            'admin' => $project->userCanEdit($user),
            'zone' => $zone,
            'sidebarBottom' => [ '/dashboard/projects' => '<i class="icon icon-2x icon-back" title="' . Text::get('profile-my_projects-header') . '"></i> ' . Text::get('profile-my_projects-header') ]
        ]);
        return true;
    }

    protected function validateProject($pid = null, $section = 'summary', $lang = null, &$form = null) {

        // Old Compatibility with session value
        // TODO: remove this when legacy is removed
        if(!$pid) {
            $pid = Session::get('project');

            // If empty project, get one of mine
            list($project) = Project::ofmine($this->user->id, false, 0, 1);
            if($project) {
                $pid = $project->id;
            }
            if($pid) {
                return $this->redirect("/dashboard/project/{$pid}/$section");
            }
        }
        // Get project
        $this->project = Project::get( $pid, $lang );
        // TODO: implement translation permissions
        if(!$this->project instanceOf Project || !$this->project->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        static::createProjectSidebar($this->project, $section, $form);

        $this->admin = $this->project->userCanModerate($this->user);

        return $this->project;
    }

    public function indexAction(Request $request) {

        // mis proyectos
        $projects = Project::ofmine($this->user->id, false, 0, 3);
        $projects_total = Project::ofmine($this->user->id, false, 0, 0, true);

        return $this->viewResponse('dashboard/projects', [
            'projects' => $projects,
            'projects_total' => $projects_total,
        ]);
    }

    public function summaryAction($pid = null, Request $request) {
        $project = $this->validateProject($pid, 'summary');
        if($project instanceOf Response) return $project;

        return $this->viewResponse('dashboard/project/summary', [
            'statuses' => Project::status(),
            'status_text' => $status_text,
            'status_class' => $status_class,
            'desc' => $desc
        ]);
    }

    /**
     * Returns the link where to redirect after a form submission
     * goto next step with errors
     * returns to summary if is approved or no errors
     */
    protected function getEditRedirect($current = null, Request $request = null) {
        $goto = 'summary';
        $validate = $request && $request->query->has('validate');
        if(!$this->project->isApproved()) {
            $validation = $this->project->getValidation();
            $steps = ['profile' , 'overview', 'images', 'costs', 'rewards', 'campaign'];
            $pos = array_search($current, $steps);
            if($pos > 0) {
                $steps = array_merge(
                        array_slice($steps, $pos + 1),
                        array_slice($steps, 0, $pos)
                    );
            }
            // print_r($steps);die;
            if($validation->global < 100) {
                foreach($steps as $i => $step) {
                    if($validation->{$step} < 100) {
                        $goto = $step;
                        break;
                    }
                }
            }

        }
        return '/dashboard/project/' . $this->project->id . '/' . $goto . ($validate ? '?validate' : '');
    }

    /**
     * Project edit (personal)
     * NOTE: Step removed, maintaining the method just in case is comming back some day
     */
    public function personalAction($pid, Request $request) {
        $project = $this->validateProject($pid, 'personal');
        if($project instanceOf Response) return $project;

        $user = $project->getOwner();
        $defaults = (array) $project;

        if($account = Account::get($project->id)) {
            $defaults['paypal'] = $account->paypal;
            $defaults['bank'] = $account->bank;
        }
        if($personal = (array)User::getPersonal($user)) {
            foreach($personal as $k => $v) {
                if(array_key_exists($k, $defaults) && empty($defaults[$k])) {
                    $defaults[$k] = $v;
                }
            }
        }

        // Create the form
        $processor = $this->getModelForm('ProjectPersonal', $project, $defaults, ['account' => $account], $request);
        // $processor->setReadonly(!$project->userCanEdit($this->user, true))->createForm();
        $processor->setReadonly(!($this->admin || $project->inEdition()))->createForm();
        $processor->getBuilder()
            ->add('submit', 'submit', [
                'label' => $project->isApproved() ? 'regular-submit' : 'form-next-button'
            ]);

        $form = $processor->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form, true);
                Message::info(Text::get('user-personal-saved'));
                return $this->redirect($this->getEditRedirect('personal', $request));
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }
        return $this->viewResponse('dashboard/project/personal', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Project edit (overview)
     */
    public function overviewAction($pid, Request $request) {
        $project = $this->validateProject($pid, 'overview');
        if($project instanceOf Response) return $project;

        $defaults = (array)$project;

        // Create the form
        $processor = $this->getModelForm('ProjectOverview', $project, $defaults, [], $request);
        // For everyone
        $processor->setReadonly(!($this->admin || $project->inEdition()))->createForm();
        // Just for the owner
        // $processor->setReadonly(!$project->userCanEdit($this->user, true))->createForm();

        if(!$processor->getReadonly()) {
            $processor->getBuilder()->add('submit', 'submit', [
                'label' => $project->isApproved() ? 'regular-submit' : 'form-next-button'
            ]);
        }
        $form = $processor->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form, true);
                Message::info(Text::get('dashboard-project-saved'));
                return $this->redirect($this->getEditRedirect('overview', $request));
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }
        return $this->viewResponse('dashboard/project/overview', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Project edit (images)
     */
    public function imagesAction($pid = null, Request $request) {
        $project = $this->validateProject($pid, 'images');
        if($project instanceOf Response) return $project;
        $approved = $project->isApproved();

        $zones = ProjectImage::sections();
        $images = [];
        foreach ($zones as $sec => $secName) {
            if($sec === 'goal') continue;
            $images[$sec] = ProjectImage::get($project->id, $sec);
        }

        $editable = $this->admin || $project->inEdition() || $project->isAlive();
        return $this->viewResponse('dashboard/project/images' . ($editable ? '' : '_idle'), [
            'zones' => $zones,
            'images' => $images,
            'next' => $approved || !$editable ? '' : $this->getEditRedirect('images', $request)
            ]);

    }

    /**
     * Project edit (updates)
     */
    public function updatesAction($pid = null, Request $request) {

        $project = $this->validateProject($pid, 'updates');
        if($project instanceOf Response) return $project;

        $posts = [];
        $total = 0;
        $msg = '';
        $limit = 10;
        $offset = $limit * (int)$request->query->get('pag');

        $blog = Blog::get($project->id);
        if ($blog instanceOf Blog) {
            if($blog->active) {
                $posts = BlogPost::getList((int)$blog->id, false, $offset, $limit, false, $project->lang);
                $total = BlogPost::getList((int)$blog->id, false, 0, 0, true);
            }
            else {
                Message::error(Text::get('dashboard-project-blog-inactive'));
            }
        }

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));

        return $this->viewResponse('dashboard/project/updates' . ($project->isApproved() ? '' : '_idle'), [
                'posts' => $posts,
                'total' => $total,
                'limit' => $limit,
                'languages' => $languages,
                'skip' => $project->lang
            ]);
    }

    public function updatesEditAction($pid, $uid, Request $request) {
        $project = $this->validateProject($pid, 'updates');
        if($project instanceOf Response) return $project;
        $redirect = '/dashboard/project/' . $this->project->id .'/updates';

        if(!$project->isApproved()) {
            Message::error(Text::get('dashboard-project-blog-wrongstatus'));
            return $this->redirect($redirect);
        }

        $post = BlogPost::get($uid);
        if(!$post && is_null($uid)) {
            $blog = Blog::get($project->id);

            if(!$blog instanceOf Blog) {
                // Create the main blog
                $blog = new Blog([
                    'type' => 'project',
                    'owner' => $project->id,
                    'active' => true
                ]);
                if (!$blog->save($errors)) {
                    Message::error(Text::get('dashboard-project-blog-fail'). "\n" .implode("\n", $errors));
                    return $this->redirect($redirect);
                }
            }
            $post = new BlogPost([
                'blog' => $blog->id,
                'date' => date('Y-m-d'),
                'publish' => false,
                'allow' => true,
                'owner_id' => $project->id
            ]);
        } elseif($post->owner_id !== $project->id) {
            throw new ModelNotFoundException("Non matching update for project [{$project->id}]");
        }


        $defaults = (array)$post;
        // print_r($_FILES);die;
        // Create the form
        $processor = $this->getModelForm('ProjectPost', $post, $defaults, ['project' => $project]);
        // $processor->setReadonly(!$project->userCanEdit($this->user, true))->createForm();
        $processor->setReadonly(!($this->admin || $project->inEdition()))->createForm();
        $form = $processor->getBuilder()
            ->add('submit', 'submit', array(
                // 'icon_class' => null
            ))
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form);
                $this->dispatch(AppEvents::PROJECT_POST, new FilterProjectPostEvent($processor->getModel()));
                Message::info(Text::get('form-sent-success'));
                return $this->redirect($redirect);
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));

        return $this->viewResponse('dashboard/project/updates_edit', [
            'post' => $post,
            'form' => $form->createView(),
            'languages' => $languages,
            'translated' => $post->getLangsAvailable(),
            'skip' => $project->lang
            ]);

    }

    /**
    * Costs section
    */
    public function costsAction($pid, Request $request) {
        $project = $this->validateProject($pid, 'costs');
        if($project instanceOf Response) return $project;

        $defaults = (array) $project;
        // Create the form
        $processor = $this->getModelForm('ProjectCosts', $project, $defaults, [], $request);
        // $processor->setReadonly(!$project->userCanEdit($this->user, true))->createForm();
        $processor->setReadonly(!($this->admin || $project->inEdition()))->createForm();
        $builder = $processor->getBuilder();
        if(!$processor->getReadonly()) {
            $builder
                ->add('submit', 'submit', [
                    'label' => $project->isApproved() ? 'regular-submit' : 'form-next-button'
                ])
                ->add('add-cost', 'submit', [
                    'label' => 'project-add-cost',
                    'icon_class' => 'fa fa-plus',
                    'attr' => ['class' => 'btn btn-orange btn-lg add-cost']
                ]);
        }

        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            // Handle AJAX calls manually
            if($request->isXmlHttpRequest()) {
                $button = $form->getClickedButton()->getName();
                if($button === 'add-cost') {
                    if($processor->getReadonly()) {
                        return $this->rawResponse('Cost cannot be added', 'text/plain', 403);
                    }
                    $cost = new Cost(['project' => $project->id]);
                    $errors = [];
                    if(!$cost->save($errors)) {
                        return $this->rawResponse(Text::get('form-sent-error', implode(', ',$errors)), 'text/plain', 403);
                    }
                    $processor->addCost($cost);
                    return $this->viewResponse('dashboard/project/partials/cost_item', [
                        'form' => $processor->getBuilder()->getForm()->createView(),
                        // 'types' => Cost::types(),
                        'cost' => $cost
                    ]);
                }
                if(strpos($button, 'remove_') === 0) {
                    try {
                        if($processor->getReadonly()) {
                            return $this->rawResponse('Cost cannot be deleted', 'text/plain', 403);
                        }
                        $cost = Cost::get(substr($button, 7));
                        $cost->dbDelete();
                        return $this->rawResponse('deleted ' . $cost->id);
                    } catch(\PDOExpection $e) {
                        return $this->rawResponse(Text::get('form-sent-error', 'Cost not deleted'), 'text/plain', 403);
                    }
                }
            }
            try {
                $next = $form['submit']->isClicked();
                // Replace the form if delete/add buttons are pressed
                $form = $processor->save($form, true)->getBuilder()->getForm();
                Message::info(Text::get('dashboard-project-saved'));
                if($next) {
                    return $this->redirect($this->getEditRedirect('costs', $request));
                }
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('dashboard/project/costs', [
            'costs' => $project->costs,
            // 'types' => Cost::types(),
            'form' => $form->createView()
        ]);
    }

    /**
    * Rewards section
    */
    public function rewardsAction($pid = null, Request $request) {

        $project = $this->validateProject($pid, 'rewards');
        if($project instanceOf Response) return $project;

        $defaults = (array) $project;
        // Create the form
        $processor = $this->getModelForm('ProjectRewards', $project, $defaults, [], $request);
        // $processor->setReadonly(!$project->userCanEdit($this->user, true))->createForm();
        $processor->setReadonly(!($this->admin || $project->inEdition()));
        // Rewards can be added durint campaign
        if($project->inCampaign() || $project->inReview()) {
            $processor->setFullValidation(true);
        }

        $builder = $processor->createForm()->getBuilder()
            ->add('submit', 'submit', [
                'label' => $project->inEdition() ? 'form-next-button' : 'regular-submit'
            ])
            ->add('add-reward', 'submit', [
                'label' => 'project-add-reward',
                'icon_class' => 'fa fa-plus',
                'attr' => ['class' => 'btn btn-orange btn-lg add-reward']
            ]);

        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            // Handle AJAX calls manually
            if($request->isXmlHttpRequest()) {
                if(!$project->inEdition() && !$project->isAlive()) {
                    return $this->rawResponse(Text::get('dashboard-project-reward-cannot'), 'text/plain', 403);
                }
                $button = $form->getClickedButton()->getName();
                if($button === 'add-reward') {
                    $reward = new Reward(['project' => $project->id, 'type' => 'individual']);
                    $errors = [];
                    if(!$reward->save($errors)) {
                        return $this->rawResponse(Text::get('form-sent-error', implode(', ',$errors)), 'text/plain', 403);
                    }
                    $processor->addReward($reward);
                    return $this->viewResponse('dashboard/project/partials/reward_item', [
                        'form' => $processor->getBuilder()->getForm()->createView(),
                        'reward' => $reward
                    ]);
                }
                if(strpos($button, 'remove_') === 0) {
                    try {
                        $reward = Reward::get(substr($button, 7));

                        if($project->inEdition() || $reward->isDraft() || ($reward->getTaken() === 0 && $project->userCanModerate($this->user))) {
                            $reward->dbDelete();
                        } else {
                            return $this->rawResponse('Error: Reward has invests or cannot be deleted', 'text/plain', 403);
                        }
                        return $this->rawResponse('deleted ' . $reward->id);
                    } catch(\PDOExpection $e) {
                        return $this->rawResponse(Text::get('form-sent-error', 'Reward not deleted'), 'text/plain', 403);
                    }
                }
            }
            try {
                $next = $form['submit']->isClicked();
                // Replace the form if delete/add buttons are pressed
                $form = $processor->save($form, true)->getBuilder()->getForm();
                Message::info(Text::get('dashboard-project-saved'));
                if($next) {
                    return $this->redirect($this->getEditRedirect('rewards', $request));
                }
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('dashboard/project/rewards', [
            'rewards' => $project->individual_rewards,
            'form' => $form->createView()
        ]);
    }

    /** Send the project to review */
    public function applyAction($pid, Request $request) {
        $project = $this->validateProject($pid, 'summary', null, $form);
        if($project instanceOf Response) return $project;

        $referer = $request->headers->get('referer');
        if(!$referer || strpos($referer, '/dashboard/') === false) $referer ='/dashboard/project/' . $project->id . '/summary';

        $form->handleRequest($request);
        $validation = $project->inEdition() ? $project->getValidation() : false;

        if ($validation && $validation->global == 100 & $form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $project->comment = $data['message'];
            $errors = [];

            try {
                if (!$project->save($errors)) {
                    throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
                }

                // READY EVENT
                $old_id = $project->id;
                $this->dispatch(AppEvents::PROJECT_READY, new FilterProjectEvent($project));

                if(strpos($referer, $old_id) !== false) $referer = '/dashboard/project/' . $project->id . '/summary';

            } catch(\Exception $e) {
                if($project->inReview()) Message::info(Text::get('project-review-request_mail-success'));
                Message::error(Text::get('project-review-request_mail-fail') . "\n" . $e->getMessage());
            }

        } else {
            Message::error(Text::get('project-review-request_mail-fail'));
        }

        return $this->redirect($referer);
    }

    /** Delete project */
    public function deleteAction($pid, Request $request = null) {
        $project = $this->validateProject($pid);
        if($project instanceOf Response) return $project;

        $referer = $request->headers->get('referer');
        if(!$referer || strpos($referer, $project->id) !== false) $referer ='/dashboard/projects';

        if (!$project->userCanDelete($this->user)) {
            Message::error(Text::get('dashboard-project-delete-no-perms'));
            return $this->redirect($referer);
        }

        $errors = [];
        if ($project->remove($errors)) {
            Message::info(Text::get('dashboard-project-delete-ok', '<strong>' . $project->name . '</strong>'));
        } else {
            Message::error(Text::get('dashboard-project-delete-ko', '<strong>' . $project->name . '</strong>. ') . implode("\n", $errors));
        }
        return $this->redirect($referer);
    }

    /**
     * Project edit (overview)
     */
    public function campaignAction($pid, Request $request) {
        $project = $this->validateProject($pid, 'campaign');
        if($project instanceOf Response) return $project;

        $defaults = (array)$project;
        if($account = Account::get($project->id)) {
            $defaults['paypal'] = $account->paypal;
            // $defaults['bank'] = $account->bank;
        }
        if($personal = (array)User::getPersonal($user)) {
            foreach($personal as $k => $v) {
                if(array_key_exists($k, $defaults) && empty($defaults[$k])) {
                    $defaults[$k] = $v;
                }
            }
        }

        // Create the form
        $processor = $this->getModelForm('ProjectCampaign', $project, $defaults, ['account' => $account, 'user' => $this->user], $request);
        // For everyone
        $processor->setReadonly(!($this->admin || $project->inEdition()))->createForm();
        // Just for the owner
        // $processor->setReadonly(!$project->userCanEdit($this->user, true))->createForm();

        if(!$processor->getReadonly()) {
            $processor->getBuilder()->add('submit', 'submit', [
                'label' => $project->isApproved() ? 'regular-submit' : 'form-next-button'
            ]);
        }
        $form = $processor->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form, true);
                Message::info(Text::get('dashboard-project-saved'));
                return $this->redirect($this->getEditRedirect('campaign', $request));
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }
        return $this->viewResponse('dashboard/project/campaign', [
            'form' => $form->createView()
        ]);
    }

    /**
    * Collaborations section
    */
    public function supportsAction($pid = null, Request $request) {
        $project = $this->validateProject($pid, 'supports');
        if($project instanceOf Response) return $project;

        $supports = Support::getAll($project);

        $editForm = $this->createFormBuilder()
            ->add('support', 'text', [
                'label' => 'supports-field-support',
                'attr' => ['help' => Text::get('tooltip-project-support-support')],
                'constraints' => array(new Constraints\NotBlank()),
            ])
            ->add('description', 'textarea', [
                'label' => 'supports-field-description',
                'attr' => ['help' => Text::get('tooltip-project-support-description')],
                'constraints' => array(new Constraints\NotBlank()),
            ])
            ->add('id', 'hidden', [
                // 'constraints' => array(new Constraints\NotBlank())
            ])
            ->add('delete', 'hidden')
            ->add('submit', 'submit')
            ->getForm();

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            if($project->isDead()) {
                Message::error(Text::get('dashboard-project-is-dead'));
                return $this->redirect();
            }

            $data = $editForm->getData();

            // print_r($data);die;
            if($data['delete']) {
                $support = Support::get($data['delete']);
                if($support->totalThreadResponses($this->user)) {
                    Message::error(Text::get('support-remove-error-messages'));
                    return $this->redirect();
                }
                $support->dbDelete();
                Message::info(Text::get('support-removed'));
                return $this->redirect();
            }
            if($editForm->isValid()) {
                $errors = [];
                $ok = false;
                if($data['id']) {
                    $support = Support::get($data['id']);
                } else {
                    $support = new Support($data + ['project' => $this->project->id]);
                }

                if($support) {
                    $is_update = $support->thread ? true : false;
                    if($support->project === $this->project->id) {
                        $support->rebuildData($data, array_keys($editForm->all()));
                        if($ok = $support->save($errors)) {
                            // Create or update the Comment associated
                            $comment = new Comment([
                                'id' => $is_update ? $support->thread : null,
                                'user' => $this->project->owner,
                                'project' => $this->project->id,
                                'blocked' => true,
                                'message' => "{$support->support}: {$support->description}",
                                'date' => date('Y-m-d H:i:s')
                            ]);
                            $ok = $comment->save($errors);
                            // Update Support thread if needded
                            if($ok && !$support->thread) {
                                $support->thread = $comment->id;
                                $ok = $support->save($errors);
                            }
                        }
                    }
                }
                if($ok) {
                    // Send and event to create the Feed and send emails
                    if($is_update) {
                        $this->dispatch(AppEvents::MESSAGE_UPDATED, new FilterMessageEvent($comment));
                    } else {
                        $this->dispatch(AppEvents::MESSAGE_CREATED, new FilterMessageEvent($comment));
                    }
                    Message::info(Text::get('form-sent-success'));
                    return $this->redirect('/dashboard/project/' . $this->project->id . '/supports');
                } else {
                    if(empty($errors)) {
                        $errors[] = Text::get('regular-no-edit-permissions');
                    }
                    Message::error(Text::get('form-sent-error', implode(', ',$errors)));
                }
            } else {
                Message::error(Text::get('form-has-errors'));
            }
        }

        // Translations
        $transForm = $this->createFormBuilder(null, 'transform', ['attr' => ['class' => 'autoform hide-help']])
            ->add('support', 'text', [
                'label' => 'supports-field-support',
                'attr' => ['help' => Text::get('tooltip-project-support-support')],
                'required' => false
            ])
            ->add('description', 'textarea', [
                'label' => 'supports-field-description',
                'attr' => ['help' => Text::get('tooltip-project-support-description')],
                'required' => false
            ])
            ->add('id', 'hidden', [
                'constraints' => array(new Constraints\NotBlank())
            ])
            ->add('lang', 'hidden', [
                'constraints' => array(new Constraints\NotBlank())
            ])
            ->add('submit', 'submit')
            ->add('remove', 'submit', [
                'label' => Text::get('translator-delete'),
                'icon_class' => 'fa fa-trash',
                'span' => 'hidden-xs',
                'attr' => [
                    'class' => 'pull-right-form btn btn-default btn-lg',
                    'data-confirm' => Text::get('translator-delete-sure')
                    ]
            ])
            ->getForm();

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));
        $languages[$project->lang] = $langs[$project->lang];

        $transForm->handleRequest($request);
        if ($transForm->isSubmitted()) {
            if($transForm->isValid()) {
                $data = $transForm->getData();
                $lang = $data['lang'];
                foreach($project->supports as $support) {
                    if($support->id == $data['id']) break;
                }
                // Check if we want to remove a translation
                if($transForm->get('remove')->isClicked()) {
                    if($support->removeLang($lang)) {
                        Message::info(Text::get('translator-deleted-ok', $languages[$lang]));
                    } else {
                        Message::info(Text::get('translator-deleted-ko', $languages[$lang]));
                    }
                    return $this->redirect('/dashboard/project/' . $project->id . '/supports');
                }
                if($support) {
                    $errors = [];
                    if($support->setLang($lang, $data, $errors)) {
                        Message::info(Text::get('dashboard-project-support-translate-ok', [
                            '%ZONE%' => '<strong>' . Text::get('step-main') . '</strong>',
                            '%LANG%' => '<strong><em>' . $languages[$lang] . '</em></strong>'
                        ]));
                        return $this->redirect('/dashboard/project/' . $project->id . '/supports');
                    } else {
                        Message::error(Text::get('form-sent-error', implode(',',$errors)));
                    }
                } else {
                    Message::error(Text::get('form-sent-error', '-No support found-'));
                }
            } else {
                Message::error(Text::get('form-has-errors'));
            }
        }

        return $this->viewResponse('dashboard/project/supports', [
            'supports' => $supports,
            'editForm' => $editForm->createView(),
            'editFormSubmitted' => $editForm->isSubmitted(),
            'transForm' => $transForm->createView(),
            // 'transFormSubmitted' => $transForm->isSubmitted(),
            'errors' => Message::getErrors(false),
            'languages' => $languages
        ]);
    }

    /**
     * Rewards/invest section
     */
    public function investsAction($pid = null, Request $request) {
        $project = $this->validateProject($pid, 'invests');
        if($project instanceOf Response) return $project;

        if(!$project->isApproved()) {
            return $this->viewResponse('dashboard/project/invests_idle');
        }

        $limit = 25;
        $offset = $limit * (int)$request->query->get('pag');

        $order = 'invested DESC';
        list($key, $dir) = explode(' ', $request->query->get('order'));
        if(in_array($key, ['id', 'invested', 'user', 'amount', 'reward', 'fulfilled']) && in_array($dir, ['ASC', 'DESC'])) {
            $order = "$key $dir";
        }

        $filters =  [
            'reward' => ['' => Text::get('regular-see_all')],
            'others' => ['' => Text::get('regular-see_all'),
                         'pending' => Text::get('dashboard-project-filter-by-pending'),
                         'fulfilled' => Text::get('dashboard-project-filter-by-fulfilled'),
                         'donative' => Text::get('dashboard-project-filter-by-donative'),
                         'nondonative' => Text::get('dashboard-project-filter-by-nondonative')
                        ]
        ];
        foreach($project->getIndividualRewards() as $reward) {
            $filters['reward'][$reward->id] = $reward->getTitle();
        }
        if($project->getCall()) {
            $filters['others']['drop'] = Text::Get('dashboard-project-filter-by-drop');
            $filters['others']['nondrop'] = Text::Get('dashboard-project-filter-by-nondrop');
        }

        // $status = [Invest::STATUS_CHARGED, Invest::STATUS_PAID];
        // if($project->isDead()) {
        //     $status = [Invest::STATUS_RETURNED, Invest::STATUS_RELOCATED, Invest::STATUS_TO_POOL];
        // }
        $status = [Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED, Invest::STATUS_RELOCATED, Invest::STATUS_TO_POOL];
        $filter_by = ['projects' => $project->id, 'status' => $status];
        $filter = $request->query->get('filter');
        if(!is_array($filter)) $filter = [];

        if((int)$filter['reward']) {
            $filter_by['reward'] = $filter['reward'];
        }
        if(array_key_exists($filter['others'], $filters['others'])) {
            $filter_by['types'] = $filter['others'];
        }
        if($filter['query']) {
            $filter_by['name'] = $filter['query'];
        }

        $invests = Invest::getList($filter_by, null, $offset, $limit, false, $order);
        $totals = Invest::getList($filter_by, null, 0, 0, 'all');

        // TODO: save to session with current filter values?

        $messages = [];
        foreach($invests as $invest) {
            $messages[$invest->user] = Comment::getUserMessages($invest->user, $invest->project, 0, 0, true);
        }

        // print_r($messages);die;
        return $this->viewResponse('dashboard/project/invests', [
            'invests' => $invests,
            'total_invests' => $totals['invests'],
            'total_users' => $totals['users'],
            'total_amount' => $totals['amount'],
            'messages' => $messages,
            'order' => $order,
            'filters' => $filters,
            'filter' => $filter,
            'limit' => $limit
        ]);
    }

    /**
    * Analytics section
    */
    public function analyticsAction($pid = null, Request $request)
    {
        $project = $this->validateProject($pid, 'analytics');
        if($project instanceOf Response) return $project;

        $defaults = (array) $project;
        $form = $this->createFormBuilder($defaults)
            ->add('analytics_id', 'text', array(
                'label' => 'regular-analytics',
                'required' => false,
                'attr' => ['help' => Text::get('help-user-analytics')],
            ))
            ->add('facebook_pixel', 'text', array(
                'label' => 'regular-facebook-pixel',
                'required' => false,
                'attr' => ['help' => Text::get('help-user-facebook-pixel')],
            ))
            ->add('submit', 'submit', array(
                // 'icon_class' => null
            ))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $data = $form->getData();
                $project->rebuildData($data, array_keys($form->all()));
                if($project->save($errors)) {
                    // print_r($post);die;
                    Message::info(Text::get('dashboard-project-analytics-ok'));
                    return $this->redirect('/dashboard/project/' . $this->project->id .'/analytics');
                } else {
                    Message::error(Text::get('form-sent-error', implode(', ',$errors)));
                }

            } else {
                Message::error(Text::get('form-has-errors'));
            }
        }
        return $this->viewResponse('dashboard/project/analytics', ['form' => $form->createView()]);

    }

    /**
     * Social commitment
     */
    public function materialsAction($pid = null, Request $request)
    {

        $project = $this->validateProject($pid, 'materials');
        if($project instanceOf Response) return $project;

        $licenses_list = Reward::licenses();
        $icons   = Reward::icons('social');

        return $this->viewResponse('dashboard/project/shared_materials', [
            'licenses_list' => $licenses_list,
            'icons' => $icons,
            'allowNewShare' => $project->isFunded()
            ]);

    }

}
