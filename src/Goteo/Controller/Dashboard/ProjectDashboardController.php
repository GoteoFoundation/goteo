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
use Goteo\Application\View;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Blog;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Console\UsersSend;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ControllerAccessDeniedException;

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

        // Create sidebar menu
        Session::addToSidebarMenu('<i class="icon icon-2x icon-summary"></i> ' . Text::get('dashboard-menu-activity-summary'), '/dashboard/project/' . $project->id .'/summary', 'summary');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-preview"></i> ' . Text::get('regular-preview'), '/project/' . $project->id, 'preview');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-edit"></i> ' . Text::get('regular-edit'), '/project/edit/' . $project->id, 'edit');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-images"></i> ' . Text::get('images-main-header'), '/dashboard/project/' . $project->id .'/images', 'images');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-updates"></i> ' . Text::get('dashboard-menu-projects-updates'), '/dashboard/project/' . $project->id .'/updates', 'updates');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-supports"></i> ' . Text::get('dashboard-menu-projects-supports'), '/dashboard/projects/supports/select?project=' . $project->id , 'supports');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-donors"></i> ' . Text::get('dashboard-menu-projects-rewards'), '/dashboard/projects/rewards/select?project=' . $project->id, 'rewards');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-partners"></i> ' . Text::get('dashboard-menu-projects-messegers'), '/dashboard/projects/messengers/select?project=' . $project->id, 'comments');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-analytics"></i> ' . Text::get('dashboard-menu-projects-analytics'), '/dashboard/project/' . $project->id . '/analytics', 'analytics');
        Session::addToSidebarMenu('<i class="icon icon-2x icon-shared"></i> ' . Text::get('project-share-materials'), '/dashboard/project/' . $project->id .'/materials', 'materials');

        View::getEngine()->useData([
            'project' => $project,
            'admin' => $project->userCanEdit($user),
            'zone' => $zone,
            'sidebarBottom' => [ '/dashboard/projects' => '<i class="icon icon-3x icon-back" title="' . Text::get('profile-my_projects-header') . '"></i> ' . Text::get('profile-my_projects-header') ]
        ]);

    }

    protected function validateProject($pid = null, $section = 'summary') {

        // Old Compatibility with session value
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
        $this->project = Project::get( $pid );
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
            Message::error($msg);
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

        return $this->viewResponse('dashboard/project/updates', [
                'posts' => $posts,
                'total' => $total,
                'limit' => $limit,
                'errorMsg' => $msg
            ]);
    }

    public function updatesEditAction($pid, $uid = null, Request $request)
    {
        $project = $this->validateProject($pid, 'updates');
        if($project instanceOf Response) return $project;

        $post = BlogPost::get($uid);
        // if(!$post) throw new ModelNotFoundException();
        if(!$post) {
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
                'label' => 'blog-allow-comments' // Form has integrated translations
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
        return $this->viewResponse('dashboard/project/updates_edit', [
            'post' => $post,
            'form' => $form->createView()
            ]);

    }

    /**
    * Analytics section
    */
    public function analyticsAction($pid = null, Request $request)
    {
        $project = $this->validateProject($pid, 'analytics');
        if($project instanceOf Response) return $project;

        if($request->isMethod('post')) {
            $project->analytics_id = $request->request->get('analytics_id');
            $project->facebook_pixel= $request->request->get('facebook_pixel');

            if ($project->save($errors))
                Message::info(Text::get('dashboard-project-analytics-ok'));
            else
                Message::error(Text::get('dashboard-project-analytics-fail'));

        }
        // die("$pid {$project->id} {$project->name}");

        return $this->viewResponse('dashboard/project/analytics');

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
