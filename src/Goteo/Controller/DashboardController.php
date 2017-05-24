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

use Symfony\Component\HttpFoundation\Request;

use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\User\Interest;
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Library\Listing;
use Goteo\Model\License;
use Goteo\Library\Feed;
use Goteo\Console\UsersSend;


class DashboardController extends \Goteo\Core\Controller {

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    public function activityAction(Request $request) {
        return $this->viewResponse('dashboard/activity');
    }

    /**
     * Virtual wallet
     */
    public function walletAction(Request $request)
    {
        if(!Config::get('payments.pool.active')) {
            throw new \RuntimeException("Pool payment is not active!");
        }

        $user = Session::getUser();
        $pool = $user->getPool();
        $interests = Interest::getAll();

        //proyectos que coinciden con mis intereses
        $projects_suggestion = Project::favouriteCategories($user->id, 6);

        if(empty($projects_suggestion))
            $projects_suggestion=Project::published('popular', $id, 0, 6);

        return $this->viewResponse('dashboard/wallet', ['pool' => $pool, 'projects_suggestion' => $projects_suggestion, 'user_interests' => $user->interests, 'interests' => $interests, 'popular_projects' => $popular_projects, 'section' => 'pool' ]);

    }

    /**
     * Virtual wallet
     */
    public function projectsSuggestionAction(Request $request)
    {

        if ($request->isMethod('post')) {
            $interest = $request->request->get('id');
            $value= $request->request->get('value');
        }

        $user = Session::getUser();

        $user_interests=$user->interests;

        $interests = Interest::getAll();

        if($value)
            $user_interests[$interest]=$interest;
        else
            unset($user_interests[$interest]);

        $user->interests=$user_interests;

        $user->save();

        //proyectos que coinciden con mis intereses
        $projects_suggestion = Project::favouriteCategories($user->id, 6);

        return $this->viewResponse(
                'dashboard/partials/projects_suggestion',
                [   'user' => $user,
                    'interests' => $interests,
                    'user_interests' => $user_interests,
                    'projects_suggestion' => $projects_suggestion,
                    'return' => 'return'
                ]
        );
    }

     /**
     * Analytics section
     */
    public function analyticsAction(Request $request)
    {

        $user = Session::getUser();

        // Verify user projects and work project
        list($project, $projects) = Dashboard\Projects::verifyProject($user, $action, $option);

        if($request->isMethod('post')) {
            $project->analytics_id = $request->request->get('analytics_id');
            $project->facebook_pixel= $request->request->get('facebook_pixel');

            if ($project->save($errors))
                Message::info(Text::get('dashboard-project-analytics-ok'));
            else
                Message::error(Text::get('dashboard-project-analytics-fail'));

        }

        return $this->viewResponse('dashboard/analytics',
                                ['project' => $project,
                                'projects' => $projects,
                                'section' => 'analytics' ]
                );

    }

    /**
     * Social commitment
     */
    public function sharedMaterialsAction(Request $request)
    {

        $user = Session::getUser();
        $licenses_list = Project\Reward::licenses();
        $icons   = Project\Reward::icons('social');

        // Verify user projects and work project
        list($project, $projects) = Dashboard\Projects::verifyProject($user, $action, $option);

        return $this->viewResponse('dashboard/shared_materials',
                [   'project' => $project,
                    'projects' => $projects,
                    'section' => 'commitment',
                    'licenses_list' => $licenses_list,
                    'icons' => $icons,
                    'section' => 'shared-materials'
                ]
        );

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

                //Añadir siempre a Olivier.
                if (!in_array('olivier', array_keys($project_obj->getConsultants()))) {
                    $project_obj->consultants['olivier'] = 'Olivier Schulbaum';
                }

                //Añadir siempre a Manuela.
                if (!in_array('lamanuf', array_keys($project_obj->getConsultants()))) {
                    $project_obj->consultants['lamanuf'] = 'Manuela Frudà';
                }

                $project_obj->whodidit = $who;
                $project_obj->whorole = $rol;
                UsersSend::toConsultants('rewardfulfilled', $project_obj);

                return $this->viewResponse(
                    'dashboard/partials/shared_materials/save_url_modal_success'
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
                    'dashboard/partials/shared_materials/new_material_form'
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
                $licenses_list = Project\Reward::licenses();
                $project_id = $request->request->get('project_id');
                $project=Project::get($project_id);

                return $this->viewResponse(
                    'dashboard/partials/shared_materials/materials_table',
                    [
                        'project' => $project,
                        'licenses_list' => $licenses_list
                    ]
                );
            }
        }


    }


    /**
     * Licenses by type
     */
    public function getLicensesIconAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            if ($request->isMethod('post'))
            {
                $icon = $request->request->get('icon');
                $licenses=License::getAll($icon);

                return $this->viewResponse(
                    'dashboard/partials/shared_materials/license_options',
                    ['licenses' => $licenses]
                );
            }
        }


    }



}
