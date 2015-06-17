<?php

namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Model;

abstract class AbstractSubController {
    protected $request;
    protected $node;
    protected $filters;
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
    public function __construct($node, Request $request) {
        $this->request = $request;
        $this->node = $node;
    }


    public function setFilters(array $filters) {
        $this->filters = $filters;
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
    public function getUrl($action = null, $id = null) {
        $url = '/admin/' . static::getId();
        if($action) $url .= '/' .$action; // TODO: check if method exists
        if($id) $url .= '/' .$id;
        return $url;
    }

    /**
     * Returns if this class can be administred by the user in the node
     * Overwrite this function to more specific control
     */
    public static function isAllowed(Model\User $user, $node) {
        foreach($user->getAdminNodes() as $id => $user_node) {
            $has_required_role = in_array($user_node->role, static::$allowed_roles); // static refers to the called class
            // no id means all nodes allowed
            if(empty($id)) {
                // only check if the user has the required role for this module
                return $has_required_role;
            }
            // Ok if has the role and is the same node
            if($node === $id && $has_required_role) return true;
        }
        return false;
    }

    // public static function getMenu(Model\User $user, $node) {
    //     $menu = array();
    //     foreach(static::$labels as $action => $label) {
    //         // TODO: permission check
    //         $menu[$action] = $label;
    //     }
    //     return $menu;
    // }

    public function isMasterNode() {
        return Config::get('node') === $this->node;
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

    public function redirect($url = '/admin') {
        return new RedirectResponse($url);
    }

    public function response($view, $data = []) {
        return new Response(View::render($view, $data));
    }
}
