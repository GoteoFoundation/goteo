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

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterMatcherProjectEvent;

use Goteo\Controller\DashboardController;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\User;
use Goteo\Model\User\Interest as UserInterest;
use Goteo\Model\Matcher;
use Goteo\Library\Text;

class AjaxDashboardController extends DashboardController {

    /**
     * Projects suggestion by user interests (categories)
     */
    public function projectsInterestsAction(Request $request)
    {

        $offset = (int)$request->query->get('offset');
        $limit = (int)$request->query->get('limit');
        if(empty($limit)) $limit = 6;

        $user = User::get(Session::getUserId());

        if ($request->isMethod('post')) {
            $interest = $request->request->get('id');
            $value = $request->request->get('value');
            if($value) {
                $user->interests[$interest] = new UserInterest(['interest' => $interest]);
            } else {
                unset($user->interests[$interest]);
            }
            // print_r($user->interests);die("$interest $value");
            $user->save();
            User::flush();
        }

        //proyectos que coinciden con mis intereses
        $favourite = Project::favouriteCategories($user->id, $offset, $limit);
        if($favourite) {
            $total = Project::favouriteCategories($user->id, 0, 0, true);
        } elseif($offset === 0) {
            // Special case
            $favourite = Project::published('popular', null, $offset, $limit);
            $total = Project::published('popular', null, 0, 0, true);
        }

        return $this->jsonResponse([
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'html' => View::render( 'dashboard/partials/projects_widgets_list', ['projects' => $favourite] )
        ]);
    }

    /**
     * User's projects
     */
    public function projectsMineAction(Request $request)
    {

        $offset = (int)$request->query->get('offset');
        $limit = (int)$request->query->get('limit');
        if(empty($limit)) $limit = 6;

        $userId = Session::getUserId();

        //proyectos que coinciden con mis intereses
        $projects = Project::ofmine($userId, false, $offset, $limit);
        $total = Project::ofmine($userId, false, 0, 0, true);

        return $this->jsonResponse([
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'html' => View::render( 'dashboard/partials/projects_widgets_list', ['projects' => $projects] )
        ]);

    }

    /**
     * User's invested projects
     */
    public function projectsInvestedAction(Request $request)
    {

        $offset = (int)$request->query->get('offset');
        $limit = (int)$request->query->get('limit');
        if(empty($limit)) $limit = 6;

        $userId = Session::getUserId();

        //proyectos que coinciden con mis intereses
        $projects = User::invested($userId, false, $offset, $limit);
        $total = User::invested($userId, false, 0, 0, true);

        return $this->jsonResponse([
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'html' => View::render( 'dashboard/partials/projects_widgets_list', ['projects' => $projects] )
        ]);

    }

    /**
     * get materials table
     */
    public function projectMaterialsTableAction($id, Request $request)
    {
        $project = Project::get($id);
        if(!$project->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $licenses_list = Reward::licenses();

        return $this->viewResponse(
            'dashboard/project/partials/materials/materials_table',
            [
                'project' => $project,
                'licenses_list' => $licenses_list
            ]
        );

    }

    /**
     * Accepts, rejects, activates or discards a project
     */
    public function joinMatcherAction($mid, $action, $pid, Request $request) {
        $referer = $request->headers->get('referer');
        if(!$referer) $referer = "/dashboard/project/$pid/summary";

        try {
            if($matcher = Matcher::get($mid, false)) {
                // find project in matching
                if( ! $project = $matcher->findProject($pid, 'all') ) {
                    throw new ModelNotFoundException("Not found project [$pid] in matcher [$mid]");
                }

                $status = '';
                switch($action) {
                    case 'accept': // accepted by the user
                    case 'reject': // rejected by the user
                        if($matcher->active && $project->matcher_status === 'pending' && $project->userCanEdit($this->user)) {
                            $status = $action . 'ed';
                        }
                        break;
                    case 'discard': // rejected by an admin
                    case 'activate': // activated by an admin
                        if(!in_array($project->matcher_status, ['rejected', 'active']) && $project->userCanAdmin($this->user, true)) {
                            $status = $action === 'discard' ? 'discarded' : 'active';
                        }
                        break;
                }
                if($status) {
                    $matcher->setProjectStatus($pid, $status);

                    $this->dispatch(AppEvents::MATCHER_PROJECT, new FilterMatcherProjectEvent($matcher, $project));

                    Message::info(Text::get("matcher-project-$action", '<strong>' . $matcher->name . '</strong>'));
                } else {
                    throw new ControllerAccessDeniedException("Action [$action] not allowed");
                }
            } else {
                throw new ModelNotFoundException("Inactive or not found matcher [$mid]");

            }
        } catch(\Exception $e) {
            Message::error($e->getMessage());
        }
        return $this->redirect($referer);
    }

}
