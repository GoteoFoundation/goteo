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

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Library\Feed;
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\User;
use Goteo\Model\Log;
use Goteo\Controller\Admin\AdminControllerInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class AdminController extends \Goteo\Core\Controller {

    private static $subcontrollers = [];
    private static $context_vars = [];
    private static $groups = [
        'activity' => ['text' => 'admin-activity', 'icon' => '<i class="fa fa-2x fa-fax"></i>', 'position' => 10],
        'communications' => ['text' => 'admin-communications', 'icon' => '<i class="fa fa-2x fa-send"></i>', 'position' => 20],
        'contents' => ['text' => 'admin-contents', 'icon' => '<i class="fa fa-2x fa-font"></i>', 'position' => 30],
        'services' => ['text' => 'admin-services', 'icon' => '<i class="fa fa-2x fa-globe"></i>', 'position' => 40],
        'main' => ['text' => 'admin-home', 'icon' => '<i class="fa fa-2x fa-home"></i>', 'position' => 60],
        'others' => ['text' => 'admin-others', 'icon' => '<i class="fa fa-2x fa-folder"></i>', 'position' => 100]
    ];
    private static $legacy_groups = [
        'activity' => ['recent', 'projects', 'accounts', 'rewards'],
        'main' => ['home', 'promote', 'news', 'banners', 'footer', 'open_tags', 'stories'],
        'contents' => ['node', 'texts', 'faq', 'pages', 'categories', 'social_commitment', 'licenses', 'icons', 'tags', 'criteria', 'glossary', 'info', 'wordcount', 'milestones'],
        'communications' => ['blog', 'newsletter', 'mailing', 'sent', 'templates'],
        'services' => ['sponsors', 'nodes', 'transnodes', 'calls', 'workshop', 'donor', 'reports'],
        'others' => ['reviews', 'translates', 'commons']
    ];

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    /**
     * Controller for route /admin
     */
    public function indexAction(Request $request) {
        $user = self::getCurrentUser($request);
        static::createAdminSidebar($user);
        $links = $legacy = [];
        foreach (static::$subcontrollers as $id => $class) {
            if(in_array('Goteo\Controller\Admin\AdminControllerInterface', class_implements($class))) {
                $links["/admin/$id"] = $class::getLabel('html');
            } else {
                $legacy["/admin/$id"] = $class::getLabel();
            }
        }
        return $this->viewResponse('admin/index', ['links' => $links, 'legacy' => $legacy, 'sidebar' => Session::getSidebarMenu()]);
    }
    /**
     * Controller for any route under /admin/{route}

     */
    public function routingAction($id, $uri = '', Request $request) {

        $user = self::getCurrentUser($request);
        $uri = "/$uri";

        if($module = self::getSubController($id)) {

            // Log this entry
            Log::append(['scope' => 'admin', 'target_type' => 'admin_module', 'target_id' => $id]);

            static::createAdminSidebar($user, $id, $request->getPathInfo());
            if(in_array('Goteo\Controller\Admin\AdminControllerInterface', class_implements($module))) {

                // Add the admin routes
                $module_routes = $routes = $module::getRoutes();

                if( $routes instanceOf Route) {
                    $module_routes = new RouteCollection();
                    $module_routes->add("admin-submodule-$id", $routes);
                }
                elseif( is_array($routes)) {
                    $module_routes = new RouteCollection();
                    foreach($routes as $key => $route) {
                        $module_routes->add("admin-submodule-$id-$key", $route);
                    }
                }

                // Manual matching routes
                if($module_routes instanceOf RouteCollection) {
                    $context = new RequestContext('/');
                    $matcher = new UrlMatcher($module_routes, $context);

                    try {
                        $parameters = $matcher->match($uri);
                        // print_r($parameters);die;
                        // Check permissions for this route
                        if(!$module::isAllowed($user, $uri)) {
                            throw new ControllerAccessDeniedException("User [{$user->id}] has no privileges on URI [$uri] in module [$module]");
                        }
                        // Add vars to the view
                        $this->contextVars([
                            'icon' => $module::getLabel('icon'),
                            'module_id' => $id,
                            'module_label' => $module::getLabel('text')
                        ]);
                        // Forward the subcontroller
                        return $this->forward($parameters['_controller'], $parameters);

                    } catch(ResourceNotFoundException $e) {
                        throw new NotFoundHttpException("Route [$uri] is not defined in module [$module]");
                    }

                } else {
                    throw new ControllerException("Error: [$module::getRoutes()] must return a valid instance of Symfony\Component\Routing\Route (or an array of several Routes) or Symfony\Component\Routing\RouteCollection");
                }
            } else {
                // OLD admin modules
                list($empty,$action, $sid, $subaction) = explode('/', $uri);

                return $this->optionAction($id, $action ? $action : 'list', $sid, $subaction, $request);
            }
        }
        throw new NotFoundHttpException("Admin module [$id] not found");
    }

    /**
     * Creates the sidebar in admin
     * @param  User   $user [description]
     * @param  string $zone [description]
     */
    public static function createAdminSidebar (User $user, $module_id = null, $uri = '') {

        $prefix = '/admin';
        // $modules =
        // $zone = preg_replace('|^/admin|', '', $uri);
        // die("[$uri]");

        foreach (static::$subcontrollers as $id => $class) {

            if(in_array('Goteo\Controller\Admin\AdminControllerInterface', class_implements($class))) {
                if(!$class::isAllowed($user)) continue;

                $label = $class::getLabel('html');
                $cls = strpos($label, '<i') === false ? 'nopadding' : '';
                if($sidebar = $class::getSidebar()) {
                    // echo "[$class]\n";print_r($sidebar);
                    $paths = [];
                    // Submodules returning a custom menu will have its own group
                    foreach($sidebar as $link => $route) {
                        // TODO: Apply isAllowed($user, uri)
                        if(!is_array($route)) {
                            $route = ['text' => $route, 'link' => $link];
                        }
                        $c = $route['class'] ? $route['class'] : (strpos($route['text'], '<i') === false ? 'nopadding' : '');

                        if(!$route['id']) $route['id'] = $route['link'];

                        $paths[] = ['text' => $route['text'], 'link' => $prefix . $route['link'], 'id' => $route['id'], 'class' => $c];
                    }
                    // echo "[$class|$id]\n";print_r($paths);
                    $modules[$id] = $paths;
                } else {
                    $group = $class::getGroup();
                    $init_route = ['text' => $label, 'link' => "$prefix/$id", 'id' => "/$id", 'class' => $cls];
                    $modules[$group ? $group : 'main'][] = $init_route;
                }
            }
            // Old sub-controllers
            elseif ($class::isAllowed($user, Config::get('node'))) {
                $group = 'others';
                foreach(self::$legacy_groups as $g => $ms) {
                    foreach($ms as $i) {
                        if($id === $i) {
                            $group = $g;
                            break;
                        }
                    }
                }
                $modules[$group][] = ['text' => $class::getLabel(), 'link' => "$prefix/$id", 'id' => "/$id", 'class' => 'nopadding'];
            }
        }
        // group the modules that don't define a custom menu
        $index = 1;
        $zone = '';
        $pos = -1;
        foreach($modules as $key => $paths) {
            $label = self::getGroupLabel($key, $position);
            $i = $position ? $position : $index;
            // echo "[$key: $label $position|$index:$i]\n";
            $c = strpos($label, '<i') === false ? 'nopadding' : '';
            if(count($paths) > 1) {
                Session::addToSidebarMenu($label, $paths, $key, $i, "sidebar $c");
            } else {
                // Do no make groups if only one item
                Session::addToSidebarMenu($label, $paths[0]['link'], $paths[0]['id'], $i, "$c");
            }
            $index += $position ? $position : 1;

            if($zone && $pos === -1) continue;

            foreach($paths as $p) {
                if($p['link'] === $uri) {
                    $zone = $p['id']; // Jackpot! exact route
                    $pos = -1;
                    break;
                } else {
                    $n = strpos($uri, $p['link']);
                    if($n === false) continue;
                    if($n > $pos) {
                        $zone = $p['id'];
                        $pos = $n;
                        // Do no break here just in case there's a more deep route
                    }
                }
            }
        }

        // print_r($modules);
        // echo "$module_id [zone $zone]\n";

        if($zone) {
            View::getEngine()->useData([
                'zone' => $zone,
                'sidebarBottom' => [ $prefix => '<i class="icon icon-2x icon-back"></i> ' . Text::get('admin-home') ]
            ]);
        }

    }

    public static function getGroupLabel($key, &$position = 0) {
        if(isset(self::$groups[$key])) {
            $g = self::$groups[$key];
            $position = $g['position'];
            return trim($g['icon']. ' ' . Text::get($g['text']));
        }
        if(isset(self::$subcontrollers[$key]) && in_array('Goteo\Controller\Admin\AdminControllerInterface', class_implements(self::$subcontrollers[$key]))) {
            $position = 0;
            return self::$subcontrollers[$key]::getLabel('html');
        }
        return Text::get('admin-' . $key);
    }

    private static function getCurrentUser(Request $request) {

        //refresh permission status
        User::flush();
        $user = Session::getUser();

        if (!$user) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        return $user;
    }


    /**
     * Registers a subcontroller in the admin
     * @param [type] $classname [description]
     */
    public static function addSubController($classname) {
        self::$subcontrollers[$classname::getId()] = $classname;
    }


    /**
     * Checks if a subcontroller is loaded
     * @param [type] $classname [description]
     */
    public static function existsSubController($classname) {
        foreach (static::$subcontrollers as $id => $class) {
          if($class === $classname) return true;
        }
        return false;
    }

    public static function getSubController($id) {
        return isset(self::$subcontrollers[$id]) ? self::$subcontrollers[$id] : null;
    }

    /**
     * Removes a subcontroller
     * @param  [type] $classname [description]
     */
    public static function delSubController($classname) {
        if (isset(self::$subcontrollers[$classname])) {
            unset(self::$subcontrollers[$classname]);
            return true;
        }
        foreach (self::$subcontrollers as $i => $sub) {
            if ($sub === $classname) {
                unset(self::$subcontrollers[$i]);
                return true;
            }
        }
    }

    /**
     * Returns if a user is allowed to view the admin
     * @param  User $user [description]
     * @return boolean          [description]
     */
    public static function isAllowed(User $user = null) {
        if (!$user) {
            return false;
        }

        $admin_node = Session::get('admin_node') ? Session::get('admin_node') : Config::get('node');

        foreach (static::$subcontrollers as $class) {
            if(in_array('Goteo\Controller\Admin\AdminControllerInterface', class_implements($class))) {
                if($class::isAllowed($user)) return true;
            }
            elseif ($class::isAllowed($user, $admin_node)) {
                return true;
            }
        }
        return false;
    }

    ///////////////////////////////
    /// OLD code...
    /// ////////////////////////

    public function indexOldAction(Request $request) {
        $ret = array();
        $user = self::checkCurrentUser($request);
        $this->contextVars(self::$context_vars, 'admin/');

        //feed by default for someones
        $admin_node = Session::get('admin_node');
        if ($user->hasRoleInNode($admin_node, ['superadmin', 'root']) || ($user->hasRoleInNode($admin_node, ['admin']) && Config::isMasterNode($admin_node))) {
            //TODO: allow Feed to handle multiple nodes
            $ret['feed'] = Feed::getAll('all', 'admin', 50, $admin_node);
        }
        //default admin dashboard (nothing!)
        return $this->viewResponse('admin/default', $ret);

    }


    /**
     * Old Security method
     * Gets the current user
     * Gets the menu
     * Sets the current node to admin from the user or the get Request
     * @param User $user    [description]
     * @param Request    $request [description]
     */
    private static function checkCurrentUser(Request $request, $option = null, $action = null, $id = null) {

        //refresh permission status
        User::flush();
        $user = Session::getUser();

        if (!$user) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        // all node names
        $all_nodes = Node::getList();
        // simple list of administrable nodes
        $admin_nodes = array();
        foreach ($user->getAdminNodes() as $node_id => $role) {
            $admin_nodes[$node_id] = $all_nodes[$node_id];
        }
        // all roles names
        $all_roles = User::getRolesList();

        // get working node
        $admin_node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
        if (!array_key_exists($admin_node, $admin_nodes)) {
            // back to first node
            $admin_node = key($admin_nodes);
        }

        //if need to change the current node
        if ($request->query->has('admin_node') && array_key_exists($request->query->get('admin_node'), $admin_nodes)) {
            $admin_node = $request->query->get('admin_node');
        }

        Session::store('admin_node', $admin_node);

        // Build menu from subcontrollers for the current user/node
        // Build the navigation breadcrumb
        $menu = array();
        $breadcrumb = array(['Admin', '/admin']);

        foreach (static::$subcontrollers as $class) {
            if ($class::isAllowed($user, $admin_node)) {
                $menu[$class::getId()] = $class::getLabel();
                if ($option === $class::getId()) {
                    // add option
                    $breadcrumb[] = [$class::getLabel(), $class::getUrl()];
                    // add action
                    if ($action) {
                        $breadcrumb[] = [
                            $class::getLabel($action) . ($id ? " [$id]" : ''),
                            ($action === 'list' || $id) ? '' : $class::getUrl($action, $id),
                        ];
                    }
                }
            }
        }

        self::$context_vars = [
            'option' => $option,
            'admin_menu' => $menu,
            'all_roles' => $all_roles,
            'all_nodes' => $all_nodes,
            'admin_node' => $admin_node,
            'admin_nodes' => $admin_nodes,
            'breadcrumb' => $breadcrumb,
        ];

        // If menu is not allowed, throw exception
        if (empty($menu) || ($option && !array_key_exists($option, $menu))) {
            $zone = $menu[$option] ? $menu[$option] : $option;
            if ($zone) {
                $msg = 'Access denied to <strong>' . $zone . '</strong>';
            } else {
                $msg = 'Access denied!';
            }

            Message::error($msg);
            throw new ControllerAccessDeniedException($msg);
        }

        return $user;
    }

    // Old dispatcher for submodules
    // preparado para index unificado
    //
    public function optionAction($option, $action = 'list', $id = null, $subaction = null, Request $request) {
        View::setTheme('default');

        $ret = array();
        $SubC = static::$subcontrollers[$option];

        // 'Goteo\Controller\Admin\\' . \strtoCamelCase($option) . 'SubController';

        try {
            $user = self::checkCurrentUser($request, $option, $action, $id);

            if (!class_exists($SubC)) {
                return $this->viewResponse('admin/denied', ['msg' => "Class [$SubC] not found for path [$option]"], Response::HTTP_BAD_REQUEST);
            }
            $node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
            $controller = new $SubC($node, $user, $request);
            $method = $action . 'Action';
            if (!method_exists($controller, $method)) {
                return $this->viewResponse('admin/denied', ['msg' => "Method [$method()] not found for class [$SubC]"], Response::HTTP_BAD_REQUEST);
            }
            $ret = $controller->$method($id, $subaction);

        } catch (ControllerAccessDeniedException $e) {
            // Instead of the default denied page, redirect to login
            Message::error($e->getMessage());
            $url = $request->getPathInfo();
            if (empty($url)) {
                $url = '/admin';
            }
            if (Session::isLogged()) {
                return $this->redirect('/admin');
            }
            return $this->redirect('/login?return=' . urlencode($url));
        }

        //Return the response if the subcontroller is a handy guy
        if ($ret instanceOf Response) {
            return $ret;
        }

        // If the subcontroller just specifies a template to render let's do it
        $this->contextVars(self::$context_vars, 'admin/');

        // Legacy view compatibility
        // They return a file to be rendered along with vars
        $old_path = $ret['old_view_path'];
        if (!$old_path && $ret['folder'] && $ret['file']) {
            $old_path = 'admin/' . ($ret['folder'] === 'base' ? '' : $ret['folder'] . '/') . $ret['file'] . '.html.php';
        }
        if ($old_path) {
            return $this->viewResponse('admin/simple', [
                'content' => \Goteo\Core\View::get($old_path, $ret),
            ]);
        }

        if ($ret['template']) {
            return $this->viewResponse($ret['template'], $ret + self::$context_vars);
        }

        //default admin dashboard (nothing!)
        return $this->viewResponse('admin/default', $ret);

    }

}
