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
use Goteo\Library\Text;

class SettingsDashboardController extends \Goteo\Core\Controller {
    protected $user;

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        $this->user = Session::getUser();
        $this->contextVars([
            'section' => 'settings'
        ]);
    }


    static function createSidebar($zone = '') {
        // Create sidebar menu
        Session::addToSidebarMenu( '<i class="fa fa-user-circle-o"></i> ' . Text::get('dashboard-menu-profile-profile'), '/dashboard/profile/profile', 'profile');
        Session::addToSidebarMenu( '<i class="fa fa-legal"></i> ' . Text::get('dashboard-menu-profile-personal'), '/dashboard/profile/personal', 'personal');
        Session::addToSidebarMenu( '<i class="fa fa-location-arrow"></i> ' . Text::get('dashboard-menu-profile-location'), '/dashboard/profile/location', 'location');
        Session::addToSidebarMenu( '<i class="fa fa-user-secret"></i> ' . Text::get('dashboard-menu-profile-access'), '/dashboard/profile/access', 'access');
        Session::addToSidebarMenu( '<i class="fa fa-toggle-on"></i> ' . Text::get('dashboard-menu-profile-preferences'), '/dashboard/profile/preferences', 'preferences');
        Session::addToSidebarMenu( '<i class="fa fa-vcard"></i> ' . Text::get('dashboard-menu-profile-public'), '/dashboard/profile/public', 'public');
        Session::addToSidebarMenu( '<i class="fa fa-key"></i> ' . Text::get('dashboard-menu-activity-apikey'), '/dashboard/profile/apikey', 'public');


        View::getEngine()->useData([
            'zone' => $zone,
            'sidebarBottom' => [ '/dashboard/settings' => '<i class="fa fa-reply" title="' . Text::get('dashboard-menu-profile') . '"></i> ' . Text::get('dashboard-menu-profile') ]
        ]);

    }

    /**
     * Settings
     */
    public function indexAction(Request $request)
    {
        self::createSidebar('index');
        return $this->viewResponse('dashboard/settings');
    }

}
