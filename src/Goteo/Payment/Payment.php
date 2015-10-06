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
     * Adds a payment method suitable for its use in goteo.
     * @param PaymentMethodInferface $method Class implementing interface
     */
    static public function addMethod($clas) {
        if(!in_array('Goteo\Payment\Method\PaymentMethodInterface', class_implements($clas))) {
            throw new PaymentException("Error registering class [$clas]. It must implement PaymentMethodInferface!");
        }
        // $method = new $clas();
        if(Config::get('payments.' . $clas::getId() . '.active')) self::$methods[$clas::getId()] = $clas;
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
     * Returns a instance of the method
     * @param  [type] $method [description]
     * @return [type]         [description]
     */
    static public function getMethod($method) {
        if(!isset(self::$methods[$method])) {
            throw new PaymentException("Error, payment method [$method] is not registered!");
        }
        if(!self::$methods[$method] instanceOf Goteo\Payment\Method\PaymentMethodInterface) {
            $clas = self::$methods[$method];
            self::$methods[$method] = new $clas(Session::getUser());
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
