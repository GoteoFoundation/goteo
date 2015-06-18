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

        // Array de usuarios con permisos especiales
        static public $supervisors = array(
            'contratos' => array(
                // paneles de admin permitidos
                'commons'
            )
        );

        private static $subcontrollers = array();

        public static function addSubController($classname) {
            self::$subcontrollers[] = $classname;
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
            if ( ! $user || empty($user->getAdminNodes())) {
                throw new ControllerAccessDeniedException("Access denied! User has no permissions");
            }


            // get working node
            $nodes = array();
            foreach($user->getAdminNodes() as $node_id => $node) {
                $nodes[$node_id] = $node->name;
            }
            $node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
            //if need to change the current node
            if($request->query->has('node') && array_key_exists($request->query->get('node'), $nodes)) {
                $node = $request->query->get('node');
                Session::store('admin_node', $node);
            }


            // Build menu from subcontrollers for the current user/node
            // Build the navigation breadcrumb
            $menu = array();
            $breadcrumb = array(['Admin', '/admin']);

            foreach(static::$subcontrollers as $class) {
                if($class::isAllowed($user, $node)) {
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
                    'admin_node' => $node,
                    'admin_nodes' => $nodes,
                    'breadcrumb' => $breadcrumb
                ]);

            // If menu is not allowed, throw exception
            if($option && ! array_key_exists($option, $menu) ) {
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
                // $controller->setFilters(self::setFilters($option));
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

        /*
         * Si no tenemos filtros para este gestor los cogemos de la sesion
         */

        private static function setFilters($option) {

            // array de fltros para el sub controlador
            $filters = array();
            if(!is_array(self::$options[$option]['filters'])) return $filters;

            if (isset($_GET['reset']) && $_GET['reset'] == 'filters') {
                unset($_SESSION['admin_filters'][$option]);
                unset($_SESSION['admin_filters']['main']);
                foreach (self::$options[$option]['filters'] as $field => $default) {
                    $filters[$field] = $default;
                }
                return $filters;
            }

            // si hay algun filtro
            $filtered = false;

            // filtros de este gestor:
            // para cada uno tenemos el nombre del campo y el valor por defecto
            foreach (self::$options[$option]['filters'] as $field => $default) {
                if (isset($_GET[$field])) {
                    // si lo tenemos en el get, aplicamos ese a la sesión y al array
                    $filters[$field] = (string) $_GET[$field];
                    $_SESSION['admin_filters'][$option][$field] = (string) $_GET[$field];
                    if (($option == 'reports' && $field == 'user')
                            || ($option == 'projects' && $field == 'user')
                            || ($option == 'users' && $field == 'name')
                            || ($option == 'accounts' && $field == 'name')
                            || ($option == 'rewards' && $field == 'name')) {

                        $_SESSION['admin_filters']['main']['user_name'] = (string) $_GET[$field];
                    }
                    $filtered = true;
                } elseif (!empty($_SESSION['admin_filters'][$option][$field])) {
                    // si no lo tenemos en el get, cogemos de la sesion pero no lo pisamos
                    $filters[$field] = $_SESSION['admin_filters'][$option][$field];
                    $filtered = true;
                } else {
                    // a ver si tenemos un filtro equivalente
                    switch ($option) {
                        case 'projects':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                        case 'users':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                        case 'accounts':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                        case 'rewards':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                    }

                    // si no tenemos en sesion, ponemos el valor por defecto
                    if (empty($filters[$field])) {
                        $filters[$field] = $default;
                    }
                }
            }

            if ($filtered) {
                $filters['filtered'] = 'yes';
            }

            return $filters;
        }

    }

}
