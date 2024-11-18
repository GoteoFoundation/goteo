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
use Goteo\Payment\Method\StripeSubscriptionPaymentMethod;

/**
 * A statically defined class to manage payments
 *
 * Payments use methods must implement
 * Goteo\Payment\Method\PaymentMethodInterface
 */
class Payment {

    static protected array $methods = [];
    static protected string $default_method = '';

    /**
     * Adds a payment method suitable for its use in goteo.
     */
    static public function addMethod(string $class, bool $force = false) {
        if(!in_array(PaymentMethodInterface::class, class_implements($class))) {
            throw new PaymentException("Error registering class [$class]. It must implement PaymentMethodInterface!");
        }

        $config = Config::get("payments");
        // adding method
        if($force || $config[$class::getId()]['active']) {
            self::$methods[$class::getId()] = $class;
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

    static public function removeMethod($id): bool
    {
        if(array_key_exists($id, self::$methods)) {
            unset(self::$methods[$id]);
            return true;
        }
        foreach(self::$methods as $i => $class) {
            $cmp = $class;
            if($class instanceOf PaymentMethodInterface) {
                $cmp = get_class($class);
            }
            if($class === $cmp) {
                unset(self::$methods[$i]);
                return true;
            }
        }

        throw new PaymentException("Error de-registering method [$id]");
    }

    /**
     * Returns all available payment methods
     * @return PaymentMethodInterface[]
     */
    static public function getMethods(User $user = null): array
    {
        // instantiate methods if valid user passed
        if($user) {
            foreach(self::$methods as $id => $class) {
                self::$methods[$id] = new $class($user);
            }
        }
        return self::$methods;
    }

    static public function methodExists($method): bool
    {
        return isset(self::$methods[$method]);
    }

    static public function getMethod($method, User $user = null): PaymentMethodInterface {
        if(!self::methodExists($method)) {
            throw new PaymentException("Error, payment method [$method] is not registered!");
        }
        if(!self::$methods[$method] instanceOf PaymentMethodInterface) {
            $class = self::$methods[$method];
            self::$methods[$method] = new $class($user ?: Session::getUser());
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

    /**
     * @return PaymentMethodInterface[]
     */
    static public function getSubscriptionMethods(): array
    {
        return array_filter(self::$methods, function($method) {
            switch (\get_class($method)) {
                case StripeSubscriptionPaymentMethod::class:
                    return true;
                default:
                    return false;
                    break;
            }
        });
    }

    static public function isSubscriptionMethod(string $method): bool
    {
        if (!self::getMethod($method)) return false;

        $name = $method;
        return 0 < count(array_filter(self::getSubscriptionMethods(), function ($method) use ($name) {
            return $method::getId() === $name;
        }));
    }
}
