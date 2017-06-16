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
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Console\UsersSend;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ControllerAccessDeniedException;

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
        $project = Project::get( $pid );
        if(!$project instanceOf Project || !$project->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        // Create sidebar menu
        Session::addToSidebarMenu('<i class="fa fa-eye"></i> ' . Text::get('dashboard-menu-activity-summary'), '/dashboard/project/' . $pid .'/summary', 'summary');
        Session::addToSidebarMenu('<i class="fa fa-edit"></i> ' . Text::get('regular-edit'), '/project/edit/' . $pid, 'edit');
        Session::addToSidebarMenu('<i class="fa fa-image"></i> ' . Text::get('images-main-header'), '/dashboard/project/' . $pid .'/images', 'images');
        Session::addToSidebarMenu('<i class="fa fa-file-text"></i> ' . Text::get('dashboard-menu-projects-updates'), '/dashboard/projects/updates/select?project=' . $pid, 'updates');
        Session::addToSidebarMenu('<i class="fa fa-group"></i> ' . Text::get('dashboard-menu-projects-supports'), '/dashboard/projects/supports/select?project=' . $pid , 'supports');
        Session::addToSidebarMenu('<i class="fa fa-user"></i> ' . Text::get('dashboard-menu-projects-rewards'), '/dashboard/projects/rewards/select?project=' . $pid, 'rewards');
        Session::addToSidebarMenu('<i class="fa fa-comments"></i> ' . Text::get('dashboard-menu-projects-messegers'), '/dashboard/projects/messengers/select?project=' . $pid, 'comments');
        Session::addToSidebarMenu('<i class="fa fa-pie-chart"></i> ' . Text::get('dashboard-menu-projects-analytics'), '/dashboard/project/' . $pid . '/analytics', 'analytics');
        Session::addToSidebarMenu('<i class="fa fa-beer"></i> ' . Text::get('project-share-materials'), '/dashboard/project/' . $pid .'/materials', 'materials');

        return $project;
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
            'zone' => 'summary',
            'project' => $project,
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
            'project' => $project,
            'zones' => $zones,
            'images' => $images,
            'zone' => 'images'
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

        return $this->viewResponse('dashboard/project/analytics', [
            'project' => $project,
            'section' => 'analytics'
            ]);

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
            'section' => 'materials',
            'project' => $project,
            'licenses_list' => $licenses_list,
            'icons' => $icons,
            'allowNewShare' => in_array($project->status, [Project::STATUS_FUNDED , Project::STATUS_FULFILLED])
            ]);

    }

}
