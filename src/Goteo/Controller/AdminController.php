<?php

namespace Goteo\Controller {

    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Goteo\Application\Exception\ControllerAccessDeniedException;
    use Goteo\Application\Exception\ControllerException;
    use Goteo\Core\ACL,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Page,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Goteo\Application\View,
        Goteo\Application\Message,
        Goteo\Application\Session,
        Goteo\Application\Config,
        Goteo\Library\Newsletter,
        Goteo\Library\Worth;

    class AdminController extends \Goteo\Core\Controller {

        // TODO: Array de usuarios con permisos especiales
        static public $supervisors = array(
            'contratos' => array(
                // paneles de admin permitidos
                'commons'
            )
        );

        private static $subcontrollers = array();

        /**
         * Registers a subcontroller in the admin
         * @param [type] $classname [description]
         */
        public static function addSubController($classname) {
            self::$subcontrollers[] = $classname;
        }

        /**
         * Returns if a user is allowed to view the admin
         * @param  Model\User $user [description]
         * @return boolean          [description]
         */
        public static function isAllowed(Model\User $user = null) {
            if(!$user) return false;
            $admin_node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
            foreach(static::$subcontrollers as $class) {
                if($class::isAllowed($user, $admin_node)) {
                    return true;
                }
            }
            return false;
        }
        /**
         * Security method
         * Gets the current user
         * Gets the menu
         * Sets the current node to admin from the user or the get Request
         * @param Model\User $user    [description]
         * @param Request    $request [description]
         */
        private static function checkCurrentUser(Request $request, $option = null, $action = null, $id = null) {

            //refresh permission status
            Model\User::flush();
            $user = Session::getUser();

            if ( ! $user ) {
                throw new ControllerAccessDeniedException("Access denied! User has no permissions");
            }

            // all node names
            $all_nodes = Model\Node::getList();
            // simple list of administrable nodes
            $admin_nodes = array();
            foreach($user->getAdminNodes() as $node_id => $role) {
                $admin_nodes[$node_id] = $all_nodes[$node_id];
            }
            // all roles names
            $all_roles = Model\User::getRolesList();

            // get working node
            $admin_node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
            //if need to change the current node
            if($request->query->has('admin_node') && array_key_exists($request->query->get('admin_node'), $admin_nodes)) {
                $admin_node = $request->query->get('admin_node');
                Session::store('admin_node', $admin_node);
            }


            // Build menu from subcontrollers for the current user/node
            // Build the navigation breadcrumb
            $menu = array();
            $breadcrumb = array(['Admin', '/admin']);

            foreach(static::$subcontrollers as $class) {
                if($class::isAllowed($user, $admin_node)) {
                    $menu[$class::getId()] = $class::getLabel();
                    if($option === $class::getId()) {
                        // add option
                        $breadcrumb[] = [$class::getLabel(), $class::getUrl()];
                        // add action
                        if($action)
                            $breadcrumb[] = [
                                    $class::getLabel($action) . ($id ? " [$id]" : ''),
                                    ($action === 'list' || $id) ? '' : $class::getUrl($action, $id)
                                ];
                    }
                }
            }

            View::getEngine()->useContext('admin/', [
                    'option' => $option,
                    'admin_menu' => $menu,
                    'all_roles' => $all_roles,
                    'all_nodes' => $all_nodes,
                    'admin_node' => $admin_node,
                    'admin_nodes' => $admin_nodes,
                    'breadcrumb' => $breadcrumb,
                    ]);

            // If menu is not allowed, throw exception
            if(empty($menu) || ($option && ! array_key_exists($option, $menu)) ) {
                $zone = $menu[$option] ? $menu[$option] : $option;
                if($zone) $msg = 'Access denied to <strong>' . $zone . '</strong>';
                else      $msg = 'Access denied!';
                Message::error($msg);
                throw new ControllerAccessDeniedException($msg);
            }

            return $user;
        }

        /** Default index action */
        public function indexAction(Request $request) {
            $ret = array();
            try {
               $user = self::checkCurrentUser($request);
            } catch(ControllerAccessDeniedException $e) {
                // Instead of the default denied page, redirect to login
                Message::error($e->getMessage());
                return new RedirectResponse('/user/login');
            }

            //feed by default for someones
            if($nodes = $user->getAdminNodes()) {
                //TODO: allow Feed to handle multiple nodes
                $ret['feed'] = \Goteo\Library\Feed::getAll('all', 'admin', 50, Session::get('admin_node'));
            }
            //default admin dashboard (nothing!)
            return new Response(View::render('admin/default', $ret));

        }

        // preparado para index unificado
        public function optionAction($option, $action = 'list', $id = null, $subaction = null, Request $request) {
            $ret = array();
            $SubC = 'Goteo\Controller\Admin\\' . \strtoCamelCase($option) . 'SubController';

            try {
                $user = self::checkCurrentUser($request, $option, $action, $id);
                if( ! class_exists($SubC) ) {
                    return new Response(View::render('admin/denied', ['msg' => "Class [$SubC] not found"]), Response::HTTP_BAD_REQUEST);
                }
                $node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
                $controller = new $SubC($node, $user, $request);
                $method = $action . 'Action';
                if( ! method_exists($controller, $method) ) {
                    return new Response(View::render('admin/denied', ['msg' => "Method [$method()] not found for class [$SubC]"]), Response::HTTP_BAD_REQUEST);
                }
                $ret = $controller->$method($id, $subaction);

            } catch(ControllerAccessDeniedException $e) {
                // Instead of the default denied page, redirect to login
                Message::error($e->getMessage());
                return new RedirectResponse('/admin');
            }

            //Return the response if the subcontroller is a handy guy
            if($ret instanceOf Response) {
                return $ret;
            }

            // Old view compatibility
            // They return a file to be rendered along with vars
            $old_path = $ret['old_view_path'];
            if(!$old_path && $ret['folder'] && $ret['file']) {
                $old_path = 'admin/' . ($ret['folder'] === 'base' ? '' : $ret['folder'] . '/') . $ret['file'].'.html.php';
            }
            if ($old_path) {
                return new Response(View::render('admin/simple', [
                    'content' => \Goteo\Core\View::get($old_path, $ret)
                    ]));
            }

            // If the subcontroller just specifies a template to render let's do it
            if ($ret['template']) {
                  return new Response(View::render($ret['template'], $ret));
            }

            //default admin dashboard (nothing!)
            return new Response(View::render('admin/default', $ret));

        }

    }

}
