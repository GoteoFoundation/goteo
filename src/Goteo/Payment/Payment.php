<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Payment;

use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Model\user;

use Goteo\Payment\Method\PaymentMethodInterface;

/**
 * A statically defined class to manage payments
 *
 * Payments uses methods must implement
 * Goteo\Payment\Method\PaymentMethodInterface
 */
class Payment {
    static protected $methods = [];
    static protected $default_method = '';

    /**
     *
     * Adds a payment method suitable for its use in goteo.
     * @param Goteo\Payment\Method\PaymentMethodInterface $clas  The payment class
     * @param boolean $force whether to check config or not
     */
    static public function addMethod($clas, $force = false) {
        if(!in_array('Goteo\Payment\Method\PaymentMethodInterface', class_implements($clas))) {
            throw new PaymentException("Error registering class [$clas]. It must implement PaymentMethodInferface!");
        }

        $config = Config::get("payments");
        // adding method
        if($force || $config[$clas::getId()]['active']) {
            self::$methods[$clas::getId()] = $clas;
        }

        $methods = [];
        // Keeping settings.yml order
        foreach($config as $id => $vars) {
            if($vars['active'] && array_key_exists($id, self::$methods)) {
                $methods[$id] = self::$methods[$id];
            }
        }
        // adding non config
        foreach(self::$methods as $id => $c) {
            if(!array_key_exists($id, $methods)) {
                $methods[$id] = $c;
            }
        }

        self::$methods = $methods;
    }

    /**
     * Removes method
     */
    static public function removeMethod($id) {
        if(array_key_exists($id, self::$methods)) {
            unset(self::$methods[$id]);
            return true;
        }
        foreach(self::$methods as $i => $clas) {
            $cmp = $clas;
            if($clas instanceOf PaymentMethodInterface) {
                $cmp = get_class($clas);
            }
            if($clas === $cmp) {
                unset(self::$methods[$i]);
                return true;
            }
        }
        throw new PaymentException("Error de-registering method [$id]");
    }
    /**
     * Returns all available payment methods
     */
    static public function getMethods(User $user = null) {
        // instantiate methods if valid user passed
        if($user) {
            foreach(self::$methods as $id => $clas) {
                self::$methods[$id] = new $clas($user);
            }
        }
        return self::$methods;
    }

    /**
     * [methodExists description]
     * @param  [type] $method [description]
     * @return [type]         [description]
     */
    static public function methodExists($method) {
        return isset(self::$methods[$method]);
    }

    /**
     * Returns a instance of the method
     * @param  [type] $method [description]
     * @return [type]         [description]
     */
    static public function getMethod($method, User $user = null) {
        if(!self::methodExists($method)) {
            throw new PaymentException("Error, payment method [$method] is not registered!");
        }
        if(!self::$methods[$method] instanceOf PaymentMethodInterface) {
            $clas = self::$methods[$method];
            self::$methods[$method] = new $clas($user ? $user : Session::getUser());
        }
        return self::$methods[$method];
    }

    /**
     * Returns or sets the current method
     */
    static public function defaultMethod($method = null) {
        if($method) {
            if(!isset(self::$methods[$method])) {
                throw new PaymentException("Error while setting method [$method] as default. This method is not registered!");
            }
            self::$default_method = $method;
        }
        if(empty(self::$default_method)) {
            self::$default_method = key(self::$methods);
        }
        return self::$default_method;
    }
}
