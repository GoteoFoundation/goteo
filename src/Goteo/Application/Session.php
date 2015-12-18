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

use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class for dealing with $_SESSION related stuff
 */
class Session {
    static protected $session_expire_time = 3600;
    static protected $start_time = 0;
    static protected $triggers = array('session_expires' => null, 'session_destroyed' => null);
    static protected $request = null;

    /**
     * TODO:
     * Initializes session managem with Symfony Request object
     * @return [type] [description]
     */
    static public function factory(Request $request) {
        self::$request = $request;
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
        global $_SESSION;

        if (!isset($_SESSION)) {
            // If we are run from the command line interface then we do not care
            // about headers sent using the session_start.
            if (PHP_SAPI === 'cli') {
                $_SESSION = array();
            }
            elseif (!headers_sent()) {

                session_name($name);
                if (!session_start()) {
                   throw new Config\ConfigException(__METHOD__ . ' session_start failed.');
                }
                // Fix for session cookie time life
                ini_set('session.cookie_lifetime', self::getSessionExpires());
            } else {
                throw new Config\ConfigException(__METHOD__ . ' Session started after headers sent.');
            }
        }
        self::setStartTime(microtime(true));

        if(!self::exists('init_time')) {
            self::store('init_time', self::getStartTime());
        }
        if($session_time) {
            self::setSessionExpires($session_time);
        }
        if( self::getStartTime() > self::get('init_time') + self::getSessionExpires() ) {
            // expires session
            self::destroy(false);
            $callback = self::$triggers['session_expires'];
            if(is_callable($callback)) {
                $callback();
            }
        }
    }

    static public function getId() {
        $id = session_id();
        if($id == 'deleted') {
            session_regenerate_id();
            $id = session_id();
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
        global $_SESSION;
        if (PHP_SAPI === 'cli') {
            $_SESSION = array();
            unset($_SESSION);
        }
        else {
            session_unset();
            session_destroy();
            session_write_close();
        }
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
        global $_SESSION;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[$key] = $value;
    }

    /**
     * Retrieve some value in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function get($key, $default = null) {
        global $_SESSION;
        if(isset($_SESSION[$key])) return $_SESSION[$key];
        return $default;
    }

    /**
     * Retrieve all values in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function getAll() {
        global $_SESSION;
        return $_SESSION;
    }

    /**
     * Retrieve some value in session and deletes it afterwards
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function getAndDel($key) {
        global $_SESSION;
        $val = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $val;
    }

    /**
     * Delete some value in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function del($key) {
        global $_SESSION;
        unset($_SESSION[$key]);
        return !self::exists($key);
    }

    /**
     * Check if a value exists in session
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function exists($key) {
        global $_SESSION;
        return is_array($_SESSION) && array_key_exists($key, $_SESSION);
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
                if (!empty($user->lang)) {
                    self::store('lang', $user->lang);
                }
            }
            return $user;
        }
        return false;
    }

    /**
     * Comprueba si el usuario está identificado.
     *
     * @return boolean
     */
    static public function isLogged () {
        return (self::get('user') instanceof User);
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
}
