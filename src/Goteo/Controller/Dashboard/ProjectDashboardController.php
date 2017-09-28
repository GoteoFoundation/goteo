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
use Goteo\Application\AppEvents;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Application\Lang;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Project\Support;
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

class ProjectDashboardController extends \Goteo\Core\Controller {
    protected $user;

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        $this->user = Session::getUser();
        $this->contextVars([
            'section' => 'projects'
        ]);
    }

    static function createSidebar(Project $project, $zone = '') {
        $user = Session::getUser();
        if(!$project->userCanEdit($user)) return;

        $prefix = '/dashboard/project/' . $project->id ;

        // Create sidebar menu
        Session::addToSidebarMenu('<i class="icon icon-2x icon-summary"></i> ' . Text::get('dashboard-menu-activity-summary'), $prefix . '/summary', 'summary');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-preview"></i> ' . Text::get('regular-preview'), '/project/' . $project->id, 'preview');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-edit"></i> ' . Text::get('regular-edit'), '/project/edit/' . $project->id, 'edit');
        Session::addToSidebarMenu('<i class="fa fa-2x fa-language"></i> ' . Text::get('regular-translations'), $prefix . '/translate', 'translate');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-images"></i> ' . Text::get('images-main-header'), $prefix .'/images', 'images');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-updates"></i> ' . Text::get('dashboard-menu-projects-updates'), $prefix .'/updates', 'updates');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-supports"></i> ' . Text::get('dashboard-menu-projects-supports'), $prefix . '/supports' , 'supports');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-donors"></i> ' . Text::get('dashboard-menu-projects-rewards'), $prefix .'/invests', 'invests');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-partners"></i> ' . Text::get('dashboard-menu-projects-messegers'), '/dashboard/projects/messengers/select?project=' . $project->id, 'comments');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-analytics"></i> ' . Text::get('dashboard-menu-projects-analytics'), $prefix . '/analytics', 'analytics');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-shared"></i> ' . Text::get('project-share-materials'), $prefix .'/materials', 'materials');

        View::getEngine()->useData([
            'project' => $project,
            'admin' => $project->userCanEdit($user),
            'zone' => $zone,
            'sidebarBottom' => [ '/dashboard/projects' => '<i class="icon icon-3x icon-back" title="' . Text::get('profile-my_projects-header') . '"></i> ' . Text::get('profile-my_projects-header') ]
        ]);

    }

    protected function validateProject($pid = null, $section = 'summary', $lang = null) {

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

        self::createSidebar($this->project, $section);

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

        $status_text = '';
        // mensaje cuando, sin estar en campaña, tiene fecha de publicación
        if ($project->status < Project::STATUS_IN_CAMPAIGN && !empty($project->published)) {
            if ($project->published > date('Y-m-d')) {
                // si la fecha es en el futuro, es que se publicará
                $status_text = Text::get('project-willpublish', date('d/m/Y', strtotime($project->published)));
            } else {
                // si la fecha es en el pasado, es que la campaña ha sido cancelada
                $status_text = Text::get('project-unpublished');
            }
        } elseif ($project->status < Project::STATUS_IN_CAMPAIGN) {
            // mensaje de no publicado siempre que no esté en campaña
            $status_text = Text::get('project-not_published');
        }

        return $this->viewResponse('dashboard/project/summary', [
            'statuses' => Project::status(),
            'status_text' => $status_text
        ]);
    }


    public function imagesAction($pid = null, Request $request)
    {
        $project = $this->validateProject($pid, 'images');
        if($project instanceOf Response) return $project;

        $zones = ProjectImage::sections();
        $images = [];
        foreach ($zones as $sec => $secName) {
            $images[$sec] = ProjectImage::get($project->id, $sec);
        }
        return $this->viewResponse('dashboard/project/images', [
            'zones' => $zones,
            'images' => $images
            ]);

    }

    public function updatesAction($pid = null, Request $request)
    {
        // View::setTheme('default');
        $project = $this->validateProject($pid, 'updates');
        if($project instanceOf Response) return $project;

        $posts = [];
        $total = 0;
        $msg = '';
        $limit = 10;
        $offset = $limit * (int)$request->query->get('pag');
        if ($project->status < 3) {
            $msg = Text::get('dashboard-project-blog-wrongstatus');
            // return $this->redirect('/dashboard/projects/summary');
        } else {
            $blog = Blog::get($project->id);
            if ($blog instanceOf Blog) {
                if($blog->active) {
                    $posts = BlogPost::getList((int)$blog->id, false, $offset, $limit);
                    $total = BlogPost::getList((int)$blog->id, false, 0, 0, true);
                }
                else {
                    Message::error(Text::get('dashboard-project-blog-inactive'));
                }
            }
        }

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));

        return $this->viewResponse('dashboard/project/updates', [
                'posts' => $posts,
                'total' => $total,
                'limit' => $limit,
                'errorMsg' => $msg,
                'languages' => $languages,
                'skip' => $project->lang
            ]);
    }

    public function updatesEditAction($pid, $uid, Request $request)
    {
        $project = $this->validateProject($pid, 'updates');
        if($project instanceOf Response) return $project;

        $post = BlogPost::get($uid);

        if(!$post && is_null($uid)) {
            $blog = Blog::get($project->id);
            if(!$blog instanceOf Blog) throw new ModelException("Blog not found for project [{$project->id}]");
            $post = new BlogPost([
                'blog' => $blog->id,
                'date' => date('Y-m-d'),
                'publish' => false,
                'allow' => true
            ]);
        } elseif($post->owner_id !== $project->id) {
            throw new ModelNotFoundException("Non matching update for project [{$project->id}]");
        }


        $defaults = (array)$post;
        $defaults['date'] = new \Datetime($defaults['date']); // TODO: into the transformer datepickertype
        $defaults['allow'] = (bool) $defaults['allow'];
        $defaults['publish'] = (bool) $defaults['publish'];
        // print_r($_FILES);die;
        // Create the form
        $form = $this->createFormBuilder($defaults)
            ->add('title', 'text', array(
                'label' => 'regular-title',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 4)),
                ),
            ))
            ->add('date', 'datepicker', array(
                'label' => 'regular-date',
                'constraints' => array(new Constraints\NotBlank()),
            ))
            // saving images will add that images to the gallery
            // let's show the gallery in the field with nice options
            // for removing and reorder it
            ->add('image', 'dropfiles', array(
                'required' => false,
                'data' => $defaults['gallery'],
                'label' => 'regular-images',
                'markdown_link' => 'text',
                'accepted_files' => 'image/jpeg,image/gif,image/png',
                'url' => '/api/projects/' . $project->id . '/images',
                'constraints' => array(
                    new Constraints\Count(array('max' => 10)),
                    new Constraints\All(array(
                        // 'groups' => 'Test',
                        'constraints' => array(
                            // new Constraints\File()
                            // new NotNull(array('groups'=>'Test'))
                        )
                    ))
                )
            ))
            // ->add('gallery', 'dropfiles', array(
            //     'required' => false
            // ))
            ->add('text', 'markdown', array(
                'label' => 'regular-text',
                'required' => false,
                // 'constraints' => array(new Constraints\NotBlank()),
            ))
            ->add('media', 'media', array(
                'label' => 'regular-media',
                'required' => false
            ))
            ->add('allow', 'boolean', array(
                'required' => false,
                'label' => 'blog-allow-comments', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ->add('publish', 'boolean', array(
                'required' => false,
                'label' => 'blog-published', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ->add('submit', 'submit', array(
                // 'icon_class' => null
            ))
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                // print_r($_FILES);
                // var_dump($request->request->all());
                $data = $form->getData();
                $post->rebuildData($data);
                // var_dump($data);die;
                if($post->save($errors)) {
                    // print_r($post);die;
                    Message::info(Text::get('form-sent-success'));
                    return $this->redirect('/dashboard/project/' . $this->project->id .'/updates');
                } else {
                    Message::error(Text::get('form-sent-error', implode(', ',$errors)));
                }

            } else {
                Message::error(Text::get('form-has-errors'));
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
    * Collaborations section
    */
    public function supportsAction($pid = null, Request $request)
    {
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
            ->add('id', 'hidden')
            ->add('delete', 'hidden')
            ->add('submit', 'submit')
            ->getForm();

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            $data = $editForm->getData();
            if($data['delete']) {
                // print_r($data);die;
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
                        $support->rebuildData($data);
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

        $langs = Lang::listAll('name', false);
        $languages = array_intersect_key($langs, array_flip($project->getLangsAvailable()));

        return $this->viewResponse('dashboard/project/supports', [
            'supports' => $supports,
            'editForm' => $editForm->createView(),
            'editFormSubmitted' => $editForm->isSubmitted(),
            'errors' => Message::getErrors(false),
            'languages' => $languages
        ]);
    }

    /**
     * Rewards/invest section
     */
    public function investsAction($pid = null, Request $request)
    {
        // View::setTheme('default');
        $project = $this->validateProject($pid, 'invests');
        if($project instanceOf Response) return $project;

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

        $filter_by = ['projects' => $project->id, 'status' => [Invest::STATUS_CHARGED, Invest::STATUS_PAID]];
        $filter = $request->query->get('filter');
        if(!is_array($filter)) $filter = [];

        if((int)$filter['reward']) {
            $filter_by['reward'] = $filter['reward'];
        }
        if(array_key_exists($filter['others'], $filters['others'])) {
            $filter_by['types'] = $filter['others'];
        }

        $invests = Invest::getList($filter_by, null, $offset, $limit, false, $order);
        $totals = Invest::getList($filter_by, null, 0, 0, 'all');

        // TODO: save to session with current filter values?



        return $this->viewResponse('dashboard/project/invests', [
            'invests' => $invests,
            'total_invests' => $totals['invests'],
            'total_users' => $totals['users'],
            'total_amount' => $totals['amount'],
            'messages' => Comment::countProjectMessages($project),
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
                $project->rebuildData($data);
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
            'allowNewShare' => in_array($project->status, [Project::STATUS_FUNDED , Project::STATUS_FULFILLED])
            ]);

    }

}
