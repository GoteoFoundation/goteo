<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;

use Goteo\Application\App;
use Goteo\Model\User;
use Goteo\Library\Text;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Class for dealing with $_SESSION related stuff
 */
class Session {
    static protected $session;
    static protected $session_expire_time = 3600;
    static protected $start_time = 0;
    static protected $triggers = array('session_expires' => null, 'session_destroyed' => null);
    static protected $request = null;
    static protected $main_menu = [];
    static protected $user_menu = [];
    static protected $sidebar_menu = [];

    /**
     * Initializes session managem with Symfony Request object
     * TODO: remove request as is not needed
     * @return [type] [description]
     */
    static public function factory(Request $request = null) {
        if($request) {
            self::$request = $request;
        }
        if(!self::$session) {
            $storage = new NativeSessionStorage();
            $storage->setOptions(['gc_maxlifetime' => self::getSessionExpires()]);
            self::$session = new SymfonySession();
        }
    }

    /**
     * Returns session object
     */
    static public function getSession() {
        self::factory();
        return self::$session;
    }

    /**
     * Set the session time expirity time
     * @param [type] $time seconds
     */
    static public function setSessionExpires($time) {
        self::$session_expire_time = (int) $time;
    }

    /**
     * Set the start time
     * @param $start_time seconds
     */
    static public function setStartTime($start_time) {
        self::$start_time = (int) $start_time;
    }
    /**
     * Get the session expirity time
     * @return [type] [description]
     */
    static public function getSessionExpires() {
        return self::$session_expire_time;
    }
    /**
     * Gets the start time (when start function has been called)
     * @return [type] [description]
     */
    static public function getStartTime() {
        return self::$start_time ? self::$start_time : microtime(true);
    }
    /**
     * Gets the time when the session will expire
     * @return [type] [description]
     */
    static public function expiresIn() {
        return self::getStartTime() + self::getSessionExpires() - (int)self::get('init_time');
    }
    /**
     * Renew the init_time to extend the expire time of the current session
     * @return [type] [description]
     */
    static public function renew() {
        self::store('init_time', self::getStartTime());
    }

    /**
     * Starts session
     * @param  string $name [description]
     * @return [type]       [description]
     */
    static public function start($name = 'Goteo', $session_time = null) {
     /*   global $_SESSION;
        // Cli compatibility
        if (PHP_SAPI === 'cli') {
            $_SESSION = array();
        }*/
        if($session_time) {
            self::setSessionExpires($session_time);
        }
        try {
            if(!self::getSession()->isStarted()) {
                self::getSession()->setName($name);
                self::getSession()->start();
            }
            // Fixes session cookie time life
            // TODO: To be removed? only make it in user personal changes(password)
            // self::getSession()->migrate(false, self::getSessionExpires());
        } catch(\RuntimeException $e) {
            throw new Config\ConfigException($e->getMessage());
        }
        // print_r($_SESSION);die;
        self::setStartTime(microtime(true));

        if(!self::exists('init_time')) {
            self::store('init_time', self::getStartTime());
        }
        if( self::getStartTime() > self::get('init_time') + self::getSessionExpires() ) {
            App::getService('logger')->err('destroying session: expired', ['init_time' => self::get('init_time'), 'expires_time' => self::getSessionExpires(), 'start_time' => self::getStartTime()]);
            // expires session
            self::destroy(false);
            $callback = self::$triggers['session_expires'];
            if(is_callable($callback)) {
                $callback();
            }
        }
    }

    static public function getId() {
        $id = self::getSession()->getId();

        if($id == 'deleted') {
            self::getSession()->migrate();
            $id = self::getSession()->getId();
        }
        if($id == 'deleted') {
            throw new Config\ConfigException(__METHOD__ . ' session_id failed.');
        }
        return $id;
    }

    /**
     * Expires session
     * @return [type] [description]
     */
    static public function destroy($throw_callback = true) {
    /*    global $_SESSION;
        if (PHP_SAPI === 'cli') {
            $_SESSION = array();
            unset($_SESSION);
        }
        else {
            self::getSession()->invalidate();
        }
    */
        self::getSession()->invalidate();
        $callback = self::$triggers['session_destroyed'];
        if($throw_callback && is_callable($callback)) {
            $callback();
        }
    }

    /**
     * Stores some value in session
     * @param  [type] $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    static public function store($key, $value) {
        // Compatibilize with legacy sessions
        // TODO: to be removed once legacy migration is completed
        $_SESSION[$key] = $value;
        self::getSession()->set($key, $value);
        return self::getSession()->has($key);
    }

    /**
     * Retrieve some value in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function get($key, $default = null) {
        return self::getSession()->get($key, $default);
    }

    /**
     * Retrieve all values in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function getAll() {
        return self::getSession()->all();
    }

    /**
     * Retrieve some value in session and deletes it afterwards
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function getAndDel($key) {
        return self::getSession()->remove($key);
    }

    /**
     * Delete some value in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function del($key) {
        self::getSession()->remove($key);
        return !self::getSession()->has($key);
    }

    /**
     * Check if a value exists in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function exists($key) {
        return self::getSession()->has($key);
    }

    /**
     * Callback to execute when session expires automatically
     * @param  [type] $callback [description]
     * @return [type]           [description]
     */
    static public function onSessionExpires($callback) {
        if(is_callable($callback)) {
            self::$triggers['session_expires'] = $callback;
        }
    }
    /**
     * Callback to execute when the session is destroyed manually
     * @param  [type] $callback [description]
     * @return [type]           [description]
     */
    static public function onSessionDestroyed($callback) {
        if(is_callable($callback)) {
            self::$triggers['session_destroyed'] = $callback;
        }
    }

    /**
     * Stores a user in session
     * @param User $user Object user to store in session
     * @param boolean $full_storage sets a cookie to remember user and other sessions vars
     */
    static public function setUser(User $user, $full_storage = false) {
        if(self::store('user', $user)) {
            if($full_storage) {
                // Username remembering cookie
                Cookie::store('goteo_user', $user->id);
                // if (!empty($user->lang)) {
                //     self::store('lang', $user->lang);
                // }
            }
            return $user;
        }
        return false;
    }

    /**
     * Checks if the user is logged
     *
     * @return boolean
     */
    static public function isLogged () {
        return (self::get('user') instanceof User);
    }

    /**
     * Checks if the user is an admin
     *
     * @return boolean
     */
    static public function isAdmin () {
        if(static::isLogged()) {
            return \Goteo\Controller\AdminController::isAllowed(static::getUser());
        }
        return false;
    }

    // Returns if the user can admin some specific module
    static function isModuleAdmin($subcontroller, $node = null, User $user = null) {
        if(empty($node)) $node = Config::get('current_node');
        if(empty($user)) $user = static::getUser();
        if(static::isLogged()) {
            if($class = \Goteo\Controller\AdminController::getSubController($subcontroller)) {
                if(in_array('Goteo\Controller\Admin\AdminControllerInterface', class_implements($class))) {
                    return $class::isAllowed($user);
                }
                return $class::isAllowed($user, $node);
            }
        }
        return false;
    }

    /**
     * Returns user id if logged
     *
     * @return boolean
     */
    static public function getUserId () {
        return (self::isLogged()) ? self::get('user')->id : false;
    }

    /**
     * Returns user object if logged
     *
     * @return boolean
     */
    static public function getUser () {
        return (self::isLogged()) ? self::get('user') : null;
    }

    static protected function addToMenu(array &$menu, $item, $link = null, $id = null, $position = null, $class = null, $a_class = null) {
        if(is_array($item)) {
            if($link && !isset($item['link'])) $item['link'] = $link;
            if($id && !isset($item['id'])) $item['id'] = $id;
            if($class && !isset($item['class'])) $item['class'] = $class;
            if($a_class && !isset($item['a_class'])) $item['a_class'] = $a_class;
            $parts = $item;
        } elseif(is_array($link)) {
            // Submenus
            $parts = [ 'text' => $item, 'submenu' => $link, 'id' => $id, 'class' => $class, 'a_class' => $a_class ];
        } else {
            $parts = [ 'text' => $item, 'link' => $link, 'id' => $id, 'class' => $class, 'a_class' => $a_class ];
        }
        if(is_null($position)) {
            $position = count($menu);
        } else {
            $position = intval($position);
        }
        $menu[$position] = $parts;
        ksort($menu);
        return $menu;
    }

    static protected function delMenuPosition(array &$menu, $position) {
        unset($menu[$position]);
    }

    static public function addToMainMenu($item, $link = null, $id = null, $position = null, $class = null, $a_class = null) {
        self::addToMenu(self::$main_menu, $item, $link, $id, $position, $class, $a_class);
    }

    static public function addToUserMenu($item, $link = null, $id = null, $position = null, $class = null, $a_class = null) {
        self::addToMenu(self::$user_menu, $item, $link, $id, $position, $class, $a_class);
    }

    static public function addToSidebarMenu($item, $link = null, $id = null, $position = null, $class = null, $a_class = null) {
        self::addToMenu(self::$sidebar_menu, $item, $link, $id, $position, $class, $a_class);
    }

    static public function getMainMenu() {
        return self::$main_menu;
    }

    static public function getUserMenu() {
        return self::$user_menu;
    }

    static public function getSidebarMenu() {
        return self::$sidebar_menu;
    }

    static public function delMainMenuPosition($position) {
        return self::delMenuPosition(self::$main_menu, $position);
    }

    static public function delUserMenuPosition($position) {
        return self::delMenuPosition(self::$user_menu, $position);
    }

    static public function delSidebarMenuPosition($position) {
        return self::delMenuPosition(self::$sidebar_menu, $position);
    }
}
