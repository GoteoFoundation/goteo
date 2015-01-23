<?php

namespace Goteo\Application;

use Goteo\Model\User;

/**
 * Class for dealing with $_SESSION related stuff
 */
class Session {

    public static function setUser(User $user) {
        $_SESSION['user'] = $user;
        return $user;
    }
    /**
     * Comprueba si el usuario está identificado.
     *
     * @return boolean
     */
    public static function isLogged () {
        return (!empty($_SESSION['user']) && $_SESSION['user'] instanceof User);
    }

    /**
     * Returns user id if logged
     *
     * @return boolean
     */
    public static function getUserId () {
        return (self::isLogged()) ? $_SESSION['user']->id : false;
    }

    /**
     * Returns user object if logged
     *
     * @return boolean
     */
    public static function getUser () {
        return (self::isLogged()) ? $_SESSION['user'] : false;
    }
}
