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
use Goteo\Application\View;
use Goteo\Model\Project;
use Goteo\Model\User\Interest;
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Library\Listing;

class DashboardController extends \Goteo\Core\Controller {

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    /**
     * Virtual wallet
     */
    public function walletAction(Request $request)
    {

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

}
