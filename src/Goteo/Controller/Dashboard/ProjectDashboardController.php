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

        $submenu = [
            ['text' => '<i class="icon icon-2x icon-user"></i> ' . Text::get('step-1'), 'link' => $prefix . '/profile', 'id' => 'profile'],
            ['text' => '<i class="fa fa-2x fa-id-card-o"></i> ' . Text::get('step-2'), 'link' => $prefix . '/personal', 'id' => 'personal'],
        ];
        Session::addToSidebarMenu('<i class="fa fa-2x fa-id-badge"></i> ' . Text::get('profile-about-header'), $submenu, 'project', null, 'sidebar');

        $submenu = [
            ['text' => '<i class="icon icon-2x icon-edit"></i> ' . Text::get('step-3'), 'link' => $prefix . '/edit', 'id' => 'edit'],
            ['text' => '<i class="icon icon-2x icon-images"></i> ' . Text::get('step-3b'), 'link' => $prefix . '/images', 'id' => 'images'],
            ['text' => '<i class="fa fa-2x fa-tasks"></i> ' . Text::get('step-4'), 'link' => $prefix . '/costs', 'id' => 'costs'],
            ['text' => '<i class="fa fa-2x fa-gift"></i> ' . Text::get('step-5'), 'link' => $prefix . '/rewards', 'id' => 'rewards'],
            ['text' => '<i class="fa fa-2x fa-language"></i> ' . Text::get('regular-translations'), 'link' => $prefix . '/translate', 'id' => 'translate'],
        ];
        Session::addToSidebarMenu('<i class="icon icon-2x icon-projects"></i> ' . Text::get('project-edit'), $submenu, 'project', null, 'sidebar');
        // Session::addToSidebarMenu('<i class="fa fa-2x fa-language"></i> ' . Text::get('regular-translations'), $prefix . '/translate', 'translate');
        // Session::addToSidebarMenu('<i class="icon icon-2x icon-images"></i> ' . Text::get('images-main-header'), $prefix .'/images', 'images');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-updates"></i> ' . Text::get('dashboard-menu-projects-updates'), $prefix .'/updates', 'updates');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-supports"></i> ' . Text::get('dashboard-menu-projects-supports'), $prefix . '/supports' , 'supports');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-donors"></i> ' . Text::get('dashboard-menu-projects-rewards'), $prefix .'/invests', 'invests');
        // Session::addToSidebarMenu('<i class="icon icon-2x icon-partners"></i> ' . Text::get('dashboard-menu-projects-messegers'), '/dashboard/projects/messengers/select?project=' . $project->id, 'comments');
         $submenu = [
            ['text' => '<i class="icon icon-2x icon-analytics"></i> ' . Text::get('dashboard-menu-projects-analytics'), 'link' => $prefix . '/analytics', 'id' => 'analytics'],
            ['text' => '<i class="icon icon-2x icon-shared"></i> ' . Text::get('project-share-materials'), 'link' => $prefix . '/materials', 'id' => 'materials']
        ];
        Session::addToSidebarMenu('<i class="icon icon-2x icon-settings"></i> ' . Text::get('footer-header-resources'), $submenu, 'comments', null, 'sidebar');
        // Session::addToSidebarMenu('<i class="icon icon-2x icon-analytics"></i> ' . Text::get('dashboard-menu-projects-analytics'), $prefix . '/analytics', 'analytics');
        // Session::addToSidebarMenu('<i class="icon icon-2x icon-shared"></i> ' . Text::get('project-share-materials'), $prefix .'/materials', 'materials');

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

    /**
     * Project edit (personal)
     */
    public function personalAction($pid, Request $request)
    {
        $project = $this->validateProject($pid, 'personal');
        if($project instanceOf Response) return $project;

        $user = $project->getOwner();
        if($project->isApproved()) {
            $redirect = '/dashboard/project/' . $pid . '/personal';
        } else {
            $redirect = '/dashboard/project/' . $pid . '/edit';
            $submit_label = 'form-next-button';
        }
        $defaults = (array) $project;
        $defaults['contract_birthdate'] = new \Datetime($defaults['contract_birthdate']);
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
        $processor = $this->getModelForm('ProjectPersonal', $project, $defaults, ['account' => $account]);
        $form = $processor->getBuilder()
            ->add('submit', 'submit', [
                'label' => $submit_label ? $submit_label : 'regular-submit'
            ])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            try {
                $processor->save($form);
                Message::info(Text::get('user-personal-saved'));
                return $this->redirect($redirect);
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }
        return $this->viewResponse('dashboard/project/personal', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Project edit (edit)
     */
    public function editAction($pid, Request $request)
    {
        $project = $this->validateProject($pid, 'edit');
        if($project instanceOf Response) return $project;
        if($project->isApproved()) {
            $redirect = '/dashboard/project/' . $pid . '/edit';
        } else {
            $redirect = '/dashboard/project/' . $pid . '/images';
            $submit_label = 'form-next-button';
        }

        $defaults = (array)$project;

        // Create the form
        $processor = $this->getModelForm('ProjectEdit', $project, $defaults);
        $form = $processor->getBuilder()
            ->add('submit', 'submit', [
                'label' => $submit_label ? $submit_label : 'regular-submit'
            ])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            try {
                $processor->save($form);
                Message::info(Text::get('dashboard-project-saved'));
                return $this->redirect($redirect);
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }
        return $this->viewResponse('dashboard/project/edit', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Project edit (images)
     */
    public function imagesAction($pid = null, Request $request)
    {
        $project = $this->validateProject($pid, 'images');
        if($project instanceOf Response) return $project;
        if(!$project->isApproved()) {
            $next = '/dashboard/project/' . $pid . '/costs';
        }

        $zones = ProjectImage::sections();
        $images = [];
        foreach ($zones as $sec => $secName) {
            $images[$sec] = ProjectImage::get($project->id, $sec);
        }
        return $this->viewResponse('dashboard/project/images', [
            'zones' => $zones,
            'images' => $images,
            'next' => $next
            ]);

    }

    /**
     * Project edit (updates)
     */
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
        $defaults['date'] = new \Datetime($defaults['date']);
        $defaults['allow'] = (bool) $defaults['allow'];
        $defaults['publish'] = (bool) $defaults['publish'];
        // print_r($_FILES);die;
        // Create the form
        $processor = $this->getModelForm('ProjectPost', $post, $defaults, ['project' => $project]);
        $form = $processor->getBuilder()
            ->add('submit', 'submit', array(
                // 'icon_class' => null
            ))
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            try {
                $processor->save($form);
                Message::info(Text::get('form-sent-success'));
                return $this->redirect('/dashboard/project/' . $this->project->id .'/updates');
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
    public function costsAction($pid = null, Request $request)
    {
        $project = $this->validateProject($pid, 'costs');
        if($project instanceOf Response) return $project;
        if($project->isApproved()) {
            $redirect = '/dashboard/project/' . $pid . '/costs';
        } else {
            $redirect = '/dashboard/project/' . $pid . '/rewards';
            $submit_label = 'form-next-button';
        }

        $defaults = (array) $project;
        // Create the form
        $processor = $this->getModelForm('ProjectCosts', $project, $defaults);
        $form = $processor->getBuilder()
            ->add('submit', 'submit', [
                'label' => $submit_label ? $submit_label : 'regular-submit'
            ])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            try {
                $processor->save($form);
                Message::info(Text::get('dashboard-project-saved'));
                return $this->redirect($redirect);
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('dashboard/project/costs', [
            'costs' => $project->costs,
            'types' => Cost::types(),
            'form' => $form->createView()
        ]);
    }

    /**
    * Rewards section
    */
    public function rewardsAction($pid = null, Request $request)
    {
        $project = $this->validateProject($pid, 'rewards');
        if($project instanceOf Response) return $project;

        // $rewards = Support::getAll($project);

        return $this->viewResponse('dashboard/project/rew,ards', [
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
            ->add('id', 'hidden', [
                'constraints' => array(new Constraints\NotBlank())
            ])
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
            'allowNewShare' => in_array($project->status, [Project::STATUS_FUNDED , Project::STATUS_FULFILLED])
            ]);

    }

}
