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

        //proyectos que coinciden con mis intereses
        $favourite_categories = Project::favouriteCategories($user->id);

        if (!empty($favourite_categories)) {
            $projects_suggestion = Listing::get($favourite_categories);
        }
        else
            $popular_projects=Project::published('popular', null, 0, 6);

        return $this->viewResponse('dashboard/wallet', ['pool' => $pool, 'projects_suggestion' => $projects_suggestion, 'popular_projects' => $popular_projects, 'section' => 'pool' ]);

    }

}
