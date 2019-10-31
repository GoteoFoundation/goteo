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
use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\Project\Account;
use Goteo\Model\Project\Cost;
use Goteo\Model\Project\Reward;
use Goteo\Model\Project\Image as ProjectImage;
use Goteo\Model\Project\Support;
use Goteo\Model\User;
use Goteo\Model\Blog;
use Goteo\Model\Stories;
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

class MatcherDashboardController extends DashboardController {
    protected $user, $admin = false;

    public function __construct() {
        parent::__construct();

        $this->contextVars([
            'section' => 'matchers'
        ]);
    }

    static function createMatcherSidebar(Matcher $matcher= null, $zone = '', &$form = null) {
        $user = Session::getUser();

        //if(!$project->userCanEdit($user)) return false;
        $prefix = '/dashboard/matcher/' . $matcher->id ;


        // Create sidebar menu
        Session::addToSidebarMenu('<i class="icon icon-2x icon-summary"></i> ' . 'Matcher', $prefix . '/summary', 'summary');

        /*$validation = false;
        $admin = false;
        $validation = $project->getValidation();
        $admin = $project->userCanModerate($user) && !$project->inEdition();*/


        //if($project->inEdition() || $admin) {
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
        //}

       /* if($project->isApproved()) {
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

        if($project->isFunded()) {

            Session::addToSidebarMenu('<i class="fa fa-2x fa fa-id-badge"></i> ' . Text::get('dashboard-menu-projects-story'), $prefix . '/story', 'story');

        }

        if($project->inEdition() && $validation->global == 100) {

            Session::addToSidebarMenu('<i class="fa fa-2x fa-paper-plane"></i> ' . Text::get('project-send-review'), '/dashboard/project/' . $project->id . '/apply', 'apply', null, 'flat', 'btn btn-fashion apply-project');

        }*/

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
            //'admin' => $project->userCanEdit($user),
            'zone' => $zone,
            'sidebarBottom' => [ '/dashboard/projects' => '<i class="icon icon-2x icon-back" title="' . Text::get('profile-my_projects-header') . '"></i> ' . Text::get('profile-my_projects-header') ]
        ]);
        return true;
    }

    public function summaryAction($pid = null, Request $request) {
        /*$project = $this->validateProject($pid, 'summary');
        if($project instanceOf Response) return $project;*/

        static::createMatcherSidebar($section, $form);

        return $this->viewResponse('dashboard/matcher/summary', [
            /*'statuses' => Project::status(),
            'status_text' => $status_text,
            'status_class' => $status_class,
            'desc' => $desc*/
        ]);
    }

}