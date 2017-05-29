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
use Goteo\Model\License;
use Goteo\Console\UsersSend;
use Goteo\Controller\Dashboard;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class ProjectDashboardController extends \Goteo\Core\Controller {
    protected $user;

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        $this->user = Session::getUser();
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
        return $project;
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
            'section' => 'images'
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

        $project = $this->validateProject($pid, 'analytics');
        if($project instanceOf Response) return $project;

        $licenses_list = Reward::licenses();
        $icons   = Reward::icons('social');

        return $this->viewResponse('dashboard/project/shared_materials', [
           'project' => $project,
            'licenses_list' => $licenses_list,
            'icons' => $icons,
            'section' => 'materials'
            ]);

    }

     /**
     * Save material url
     */
    public function saveMaterialUrlAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            if ($request->isMethod('post'))
            {
                $url = $request->request->get('url');
                $reward_id= $request->request->get('reward_id');
                $project_id= $request->request->get('project');

                $user = Session::getUser();

                $reward=Reward::get($reward_id);

                $reward->url=$url;

                $reward->updateURL();

                $reward=Reward::get($reward_id);

                // For compatibility with old version of sendUsers
                $_POST['reward']=$reward_id;
                $_POST['value']=$url;

                $who = $user->id;

                $rol = "el usuario impulsor";

                // Enviar correo informativo a los asesores del proyecto.
                $project_obj = Project::getMini($project_id);

                // TODO: arreglar esta chapuza..
                // Añadir siempre a Olivier.
                if (!in_array('olivier', array_keys($project_obj->getConsultants()))) {
                    $project_obj->consultants['olivier'] = 'Olivier Schulbaum';
                }

                // Añadir siempre a Manuela.
                if (!in_array('lamanuf', array_keys($project_obj->getConsultants()))) {
                    $project_obj->consultants['lamanuf'] = 'Manuela Frudà';
                }

                $project_obj->whodidit = $who;
                $project_obj->whorole = $rol;
                UsersSend::toConsultants('rewardfulfilled', $project_obj);

                return $this->viewResponse(
                    'dashboard/project/partials/materials/save_url_modal_success'
                );
            }
        }


    }

     /**
     * Save new material
     */
    public function saveNewMaterialAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            if ($request->isMethod('post'))
            {
                $project = $request->request->get('project');
                $material = $request->request->get('material');
                $description= $request->request->get('description');
                $icon= $request->request->get('icon');
                $license= $request->request->get('license');
                $url= $request->request->get('url');

                $reward=new Reward();

                $reward->project=$project;
                $reward->reward=$material;
                $reward->description=$description;
                $reward->icon=$icon;
                $reward->license=$license;
                $reward->url=$url;
                $reward->bonus=1;
                $reward->type="social";

                $reward->save();

                return $this->viewResponse(
                    'dashboard/project/partials/materials/new_material_form'
                );
            }
        }


    }

    /**
     * Update materials table
     */
    public function updateMaterialsTableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            if ($request->isMethod('post'))
            {
                $licenses_list = Reward::licenses();
                $project_id = $request->request->get('project_id');
                $project=Project::get($project_id);

                return $this->viewResponse(
                    'dashboard/project/partials/materials/materials_table',
                    [
                        'project' => $project,
                        'licenses_list' => $licenses_list
                    ]
                );
            }
        }


    }


}
