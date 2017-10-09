<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 * Extensible class for admin sub modules
 * Default permissions:
 *     - User has to be one of 'superadmin', 'admin' or 'root' roles in the node
 */
namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Library\Text;
use Goteo\Model\User;
use Goteo\Core\Traits\LoggerTrait;

abstract class AbstractSubController extends \Goteo\Core\Controller {
    use LoggerTrait;

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
    public function __construct($node, User $user, Request $request) {
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
        if ($request->query->has('reset') && $request->query->get('reset') === 'filters') {
            unset($session_filters);
        }
        if(!is_array($session_filters)) $session_filters = array();
        if(!is_array($session_filters['main'])) $session_filters['main'] = array();
        if(!is_array($session_filters[$id])) $session_filters[$id] = array();
        // filters in session by default
        $filters = array_merge($this->filters, $session_filters[$id]);
        if(!is_array($filters)) $filters = array();

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

    /**
     * Returns the identificator for this controller
     * @return MyControllerSubController becames mycontroller
     */
    public static function getId() {
        $class = get_called_class();
        $a = explode('\\', $class);
        return strtolower(str_replace('SubController', '', end($a)));
    }
    /**
     * Returns the label for this controller
     * TODO: Text:: translation
     * @param  string $action if label is specified returns the label text instead of the general one
     */
    public static function getLabel($action = null) {
        if($action) return Text::get(static::$labels[$action]);
        return Text::get(static::$label);
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
    public static function isAllowed(User $user, $node) {
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

    /**
     * Returns true if current node/channel is the master
     * @return boolean [description]
     */
    public function isMasterNode() {
        return Config::isMasterNode($this->node);
    }

    /**
     * Returns true if the user is a translator
     * @return boolean [description]
     */
    public function isTranslator() {
        return $this->user->hasRoleInNode($this->node, ['translator']);
    }

    /**
     * Returns true if the user is at least admin
     * @return boolean [description]
     */
    public function isAdmin() {
        return $this->user->hasRoleInNode($this->node, ['admin', 'superadmin', 'root']);
    }

    /**
     * Returns true if the user is at least superadmin
     * @return boolean [description]
     */
    public function isSuperAdmin() {
        return $this->user->hasRoleInNode($this->node, ['superadmin', 'root']);
    }

    /**
     * Returns true if the user is at least root
     * @return boolean [description]
     */
    public function isRoot() {
        return $this->user->hasRoleInNode($this->node, ['root']);
    }

    /**
     * Get a var from the _GET global
     * if no var specified returns a Requests->query object
     * @param  string $var var to be retrieved (if null the full object will be returned)
     * @return mixed      The value of the var or the object
     */
    public function getGet($var = null) {
        if($var) {
            //return requested var
            return $this->request->query->get($var);
        }
        return $this->request->query;
    }
    /**
     * Checks if a var exists in _GET global
     * @param  string  $var var to be checked
     * @return boolean      true on success
     */
    public function hasGet($var) {
        return $this->request->query->has($var);
    }

    /**
     * Get a var from the _POST global
     * if no var specified returns a Requests->request object
     * @param  string $var var to be retrieved (if null the full object will be returned)
     * @return mixed      The value of the var or the object
     */
    public function getPost($var = null) {
        if($var) {
            //return requested var
            return $this->request->request->get($var);
        }
        //return object
        return $this->request->request;
    }
    /**
     * Checks if a var exists in _POST global
     * @param  string  $var var to be checked
     * @return boolean      true on success
     */
    public function hasPost($var) {
        return $this->request->request->has($var);
    }
    /**
     * Get a var from the _SERVER global
     * if no var specified returns a Requests->server object
     * @param  string $var var to be retrieved (if null the full object will be returned)
     * @return mixed      The value of the var or the object
     */
    public function getServer($var = null) {
        if($var) {
            //return requested var
            return $this->request->query->get($var);
        }
        return $this->request->server;
    }

    /**
     * Returns POST or the method used in the Request
     * @return [type] [description]
     */
    public function getMethod() {
        return $this->request->getMethod();
    }
    /**
     * True if the method is POST
     * @return boolean [description]
     */
    public function isPost() {
        return $this->request->getMethod() === 'POST';
    }

    /**
     * Get the request referer
     * @return boolean [description]
     */
    public function getReferer() {
        return $this->request->headers->get('referer');
    }

    /**
     * Returns a redirection. By default to the module administration URL
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function redirect($url = null, $status = 302) {
        if(empty($url)) {
            $url = static::getUrl();
        }
        if(is_array($url)) {
            $url = call_user_func_array("static::getUrl", $url);
        }
        return parent::redirect($url, $status);
    }

    /**
     * Returns a response for a view with passed data
     * @param  [type] $view [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function response($view, $data = []) {
        return $this->viewResponse($view, $data);
    }
}
