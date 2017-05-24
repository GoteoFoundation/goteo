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

use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Model\Project;
use Goteo\Model\User;
use Goteo\Model\User\Interest;

class AjaxDashboardController extends \Goteo\Core\Controller {

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    /**
     * Virtual wallet
     */
    public function projectsInterestsAction(Request $request)
    {

        $offset = (int)$request->query->get('offset');
        $show = $request->query->get('show');

        $user = Session::getUser();

        $interests = Interest::getAll();

        if ($request->isMethod('post')) {
            $interest = $request->request->get('id');
            $value = $request->request->get('value');
            if($value) {
                $user->interests[$interest] = $interest;
            } else {
                unset($user->interests[$interest]);
            }
            $user->save();
        }

        //proyectos que coinciden con mis intereses
        $projects_suggestion = Project::favouriteCategories($user->id, $offset, 6);
        $total_fav = Project::favouriteCategories($user->id, 0, 0, true);
        $data = [
            'interests' => $interests,
            'user_interests' => $user->interests,
            'projects' => $projects_suggestion,
            'showMore' => $total_fav > ($offset + @count($projects_suggestion)),
        ];
        if($show === 'projects') {
            unset($data['interests']);
            unset($data['user_interests']);
        }
        return $this->viewResponse( 'dashboard/partials/projects_interests', $data );
    }


}
