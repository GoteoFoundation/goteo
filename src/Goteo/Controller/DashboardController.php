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

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Model\Project;
use Goteo\Model\Message as Comment;
use Goteo\Model\User;
use Goteo\Library\Text;
use Goteo\Model\User\Interest;
use Goteo\Model\Page;

class DashboardController extends \Goteo\Core\Controller {

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        $this->user = Session::getUser();
        if(!$this->user) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

    }

    public static function createSidebar($section, $zone = '') {
        $total_messages = Comment::getUserThreads(Session::getUser(), 0, 0, true);
        if($total_messages > 0 && $section === 'activity') {
            Session::addToSidebarMenu('<i class="icon icon-2x icon-activity"></i> ' . Text::get('dashboard-menu-activity'), '/dashboard/activity', 'activity');
            Session::addToSidebarMenu('<i class="icon icon-2x icon-partners"></i> ' . Text::get('regular-messages') .' <span class="badge">' . $total_messages . '</span>', '/dashboard/messages', 'messages');
        }
        if($section === 'wallet') {
            Session::addToSidebarMenu('<i class="icon icon-2x icon-wallet-sidebar"></i> ' . Text::get('dashboard-menu-pool'), '/dashboard/wallet', 'wallet');
            Session::addToSidebarMenu('<i class="fa fa-2x fa-fw fa-download"></i> ' . Text::get('recharge-button'), '/pool', 'recharge');
        }
        View::getEngine()->useData([
            'zone' => $zone,
            'section' => $section,
            'total_messages' => $total_messages
        ]);

    }

    public function activityAction(Request $request) {
        $user = $this->user;

        self::createSidebar('activity', 'activity');

        // proyectos que cofinancio
        $invested = User::invested($user->id, false, 0, 3);
        $invested_total = User::invested($user->id, false, 0, 0, true);
        //proyectos que coinciden con mis intereses
        $favourite = Project::favouriteCategories($user->id, 0, 3);
        if($favourite) {
            $total_fav = Project::favouriteCategories($user->id, 0, 0, true);
        } else {
            $favourite = Project::published('popular', null, 0, 3);
            $total_fav = Project::published('popular', null, 0, 0, true);
        }

        $interests = Interest::getAll();

        // $page = Page::get('dashboard');
        return $this->viewResponse('dashboard/activity', [
            'section' => 'activity',
            // 'message' => str_replace('%USER_NAME%', $user->name, $page->parseContent()),
            'invested' => $invested,
            'invested_total' => $invested_total,
            'interests' => $interests,
            'user_interests' => $user->interests,
            'favourite' => $favourite,
            'favourite_total' => $total_fav,
            'limit' => 3
        ]);
    }

    public function messagesAction(Request $request) {

        $messages = Comment::getUserThreads($this->user);

        self::createSidebar('activity', 'messages');

        return $this->viewResponse('dashboard/messages', [
            'section' => 'activity',
            'messages' => $messages
        ]);
    }
    /**
     * Virtual wallet
     */
    public function walletAction(Request $request)
    {
        if(!Config::get('payments.pool.active')) {
            throw new \RuntimeException("Pool payment is not active!");
        }

        self::createSidebar('wallet', 'wallet');

        $user = $this->user;
        $pool = $user->getPool();
        $interests = Interest::getAll();

        //proyectos que coinciden con mis intereses
        $projects_suggestion = Project::favouriteCategories($user->id, 0, 6);

        if($projects_suggestion) {
            $total_fav = Project::favouriteCategories($user->id, 0, 0, true);
        } else {
            $projects_suggestion = Project::published('popular', null, 0, 6);
            $total_fav = Project::published('popular', null, 0, 0, true);
        }

        return $this->viewResponse('dashboard/wallet', [
            'pool' => $pool,
            'projects_suggestion' => $projects_suggestion,
            'projects_suggestion_total' => $total_fav,
            'user_interests' => $user->interests,
            'interests' => $interests,
            'popular_projects' => $popular_projects,
            'section' => 'wallet',
            'limit' => 6
             ]
        );

    }

}
