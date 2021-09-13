<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Core\Traits\LoggerTrait;
use Goteo\Library\Text;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

abstract class AbstractAdminController extends Controller implements AdminControllerInterface {
    use LoggerTrait;
    protected $user;

    public function __construct() {
        View::setTheme('responsive');
        View::getEngine()->useContext('admin', [
            'module' => static::getId(),
            'module_label' => static::getLabel()
        ]);
        $this->user = Session::getUser();
    }

    public static function getId() {
        $class = get_called_class();
        $a = explode('\\', $class);
        return strtolower(str_replace('AdminController', '', end($a)));
    }

    /**
     * Returns the label for this controller
     * It can return HTML content (ie: icon definition: [<i class="fa fa-user"></i> User])
     */
    public static function getLabel($mode = 'text') {
        $class = get_called_class();
        $icon = property_exists($class,'icon') ? static::$icon : '';
        if($mode === 'icon') return $icon;

        $label = property_exists($class,'label') ? static::$label : static::getId() .'-lb';
        $txt = Text::get($label);
        if($mode === 'html') return trim("$icon $txt");

        return $txt;
    }

    // Only used if getSidebar is not defined
    public static function getGroup(): string
    {
        return 'activity';
    }

    /**
     * Defaults to the main route /admin/SUBMODULE_ID to a indexAction controller
     * it can return either Route or RouteCollection instances. Any route defined here will
     * be preceded by '/admin/SUBMODULE_ID' in the url
     */
    public static function getRoutes() {
        $class = get_called_class();
        return new Route(
            '/',
            ['_controller' => "$class::indexAction"]
        );
    }

    // Only need to return paths if we want a custom group for this module
    public static function getSidebar() {
        return [
            // '/module/list' => 'Link'
        ];
    }

    /**
     * Returns if this class can be administered by specified user
     * Overwrite this function for more specific permissions
     */
    public static function isAllowed(User $user, $uri = '') {
        if($user->hasPerm('admin-any-module')) return true;
        if($user->hasPerm('admin-module-' . static::getId())) return true;
    }

    /**
     * Dummy controller, must be overwritten
     */
    public function indexAction(Request $request) {
        $id = static::getId();
        Message::error("Submodule [$id] has no routes/controllers defined yet");

        return $this->viewResponse('admin/index', []);
    }

}
