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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Model\User;
use Goteo\Library\Text;
use Goteo\Core\Traits\LoggerTrait;

abstract class AbstractAdminController extends \Goteo\Core\Controller implements AdminControllerInterface {
    use LoggerTrait;

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    /**
     * Returns the identificator for this controller
     * @return MyControllerAdminController becames mycontroller
     */
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

    public static function getGroup() {
        return 'projects';
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

    public static function getSidebar() {
        return [
            // ['text' => 'List', 'link' => '/list', 'id' => 'users-list']
        ];
    }

    /**
     * Returns if this class can be administred by specified user
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
        Message::error("Submodule [$id] not defined yet");
        return $this->viewResponse('admin/index', []);
    }

}
