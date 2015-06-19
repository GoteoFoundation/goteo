<?php
/**
 * Extensible class for admin sub modules
 * Default permissions:
 *     - User has to be one of 'superadmin', 'admin' or 'root' roles in the node
 */
namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\View;

abstract class AbstractSubController {
    protected $request;
    protected $node;
    protected $user;
    protected $filters = array();
    protected static $url;
    // Main label
    static protected $label = 'Abstract admin controller';
    // Labels for methods
    static protected $labels = array();

    /***
    * PERMISSIONS
    * Overwrite on extended classes
    ***/
    // Roles users allowed to admin this module
    static protected $allowed_roles = array('superadmin', 'admin', 'root');

    /**
     * Some defaults
     */
    public function __construct($node, \Goteo\Model\User $user, Request $request) {
        $this->request = $request;
        $this->node = $node;
        $this->user = $user;
    }


    public function setFilters(array $filters) {
        $this->filters = $filters;
    }

    public function getFilters(Request $request = null) {
        if(!is_array($this->filters)) return array();

        if(!$request) {
            $request = $this->request;
        }
        $id = static::getId();

        $session_filters = Session::get('admin_filters');
        if(!is_array($session_filters)) $session_filters = array();
        if(!is_array($session_filters['main'])) $session_filters['main'] = array();
        if(!is_array($session_filters[$id])) $session_filters[$id] = array();
        // filters in session by default
        $filters = array_intersect_key($session_filters[$id], $this->filters);
        if(!is_array($filters)) $filters = array();
        if ($request->query->has('reset') && $request->query->get('reset') === 'filters') {
            unset($filters);
            unset($session_filters['main']);
        }

        // filtros de este gestor:
        // para cada uno tenemos el nombre del campo y el valor por defecto
        foreach ($this->filters as $field => $default) {
            // in GET, overwrite
            if ($request->query->has($field)) {
                $filters[$field] = (string) $request->query->get($field);
                if (($id == 'reports' && $field == 'user')
                        || ($id == 'projects' && $field == 'user')
                        || ($id == 'users' && $field == 'name')
                        || ($id == 'accounts' && $field == 'name')
                        || ($id == 'rewards' && $field == 'name')) {

                    $session_filters['main']['user_name'] = (string) $request->query->get($field);
                }
            } else {
                // a ver si tenemos un filtro equivalente
                if(in_array($id, array('projects', 'users', 'accounts', 'rewards'))) {
                    if ($field === 'name' && $session_filters['main']['user_name']) {
                        $filters['name'] = $session_filters['main']['user_name'];
                    }
                }
            }
        }

        if ($filters !== $this->filters) {
            $filters['filtered'] = 'yes';
        }
        $session_filters[$id] = $filters;
        Session::store('admin_filters', $session_filters);
        return $filters;
    }

    public function addAllowedNode($node) {
        static::$allowed_nodes[] = $node;
    }

    /**
     * Returns the identificator for this controller
     * @return MyControllerSubController becames mycontroller
     */
    public static function getId() {
        $class = get_called_class();
        return strtolower(substr(end(explode('\\',$class)),0,-13));
    }
    /**
     * Returns the label for this controller
     * TODO: Text:: translation
     * @param  string $action if label is specified returns the label text instead of the general one
     */
    public static function getLabel($action = null) {
        if($action) return static::$labels[$action];
        return static::$label;
    }
    /**
     * Returns the url for this controller
     * @param  string $action if label is specified returns the url for the action instead of the general one
     */
    public static function getUrl($action = null, $id = null) {
        $url = '/admin/' . static::getId();
        if($action) $url .= '/' .$action; // TODO: check if method exists
        if($id) $url .= '/' .$id;
        return $url;
    }

    /**
     * Returns if this class can be administred by the user in the node
     * Overwrite this function to more specific control
     */
    public static function isAllowed(\Goteo\Model\User $user, $node) {
        foreach($user->getAdminNodes() as $id => $role) {
            $has_required_role = in_array($role, static::$allowed_roles); // static refers to the called class
            // no id means all nodes allowed
            // NOTE: This condition should not happen anymore
            if(empty($id)) {
                // only check if the user has the required role for this module
                return $has_required_role;
            }
            // Ok if has the role and is the same node
            if($node === $id && $has_required_role) return true;
        }
        return false;
    }

    // public static function getMenu(\Goteo\Model\User $user, $node) {
    //     $menu = array();
    //     foreach(static::$labels as $action => $label) {
    //         // TODO: permission check
    //         $menu[$action] = $label;
    //     }
    //     return $menu;
    // }

    public function isMasterNode() {
        return Config::isMasterNode($this->node);
    }

    public function getGet($var = null) {
        if($var) {
            //return requested var
            return $this->request->query->get($var);
        }
        return $this->request->query;
    }
    public function hasGet($var) {
        return $this->request->query->has($var);
    }

    public function getPost($var = null) {
        if($var) {
            //return requested var
            return $this->request->request->get($var);
        }
        //return object
        return $this->request->request;
    }

    public function hasPost($var) {
        return $this->request->request->has($var);
    }

    public function getServer($var = null) {
        if($var) {
            //return requested var
            return $this->request->query->get($var);
        }
        return $this->request->server;
    }

    public function getMethod() {
        return $this->request->getMethod();
    }

    public function isPost() {
        return $this->request->getMethod() === 'POST';
    }

    public function redirect($url = null) {
        if(empty($url)) {
            $url = static::getUrl();
        }
        return new RedirectResponse($url);
    }

    public function response($view, $data = []) {
        return new Response(View::render($view, $data));
    }
}
