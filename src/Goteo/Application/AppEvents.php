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

final class AppEvents
{
    /**
     * The auth.login.success event is thrown each time a user does a successful login
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterAuthEvent instance.
     *
     * @var string
     */
    const LOGIN_SUCCEEDED = 'auth.login.success';

    /**
     * The auth.login.fail event is thrown each time a user attemps to login and fails
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterAuthEvent instance.
     *
     * @var string
     */
    const LOGIN_FAILED = 'auth.login.fail';

    /**
     * The auth.login.logged event is thrown each time a user attemps to login and fails
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterAuthEvent instance.
     *
     * @var string
     */
    const ALREADY_LOGGED = 'auth.login.logged';

    /**
     * The auth.signup.success event is thrown each time a user completes a successful signup
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterAuthEvent instance.
     *
     * @var string
     */
    const SIGNUP_SUCCEEDED = 'auth.signup.success';

    /**
     * The auth.signup.fail event is thrown each time a user attemps to signup and fails
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterAuthEvent instance.
     *
     * @var string
     */
    const SIGNUP_FAILED = 'auth.signup.fail';


    /**
     * The auth.logout event is thrown each time a user logouts the application
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterAuthEvent instance.
     *
     * @var string
     */
    const LOGOUT = 'auth.logout';

    /**
     * The invest.init event is thrown each time a user starts a payment process
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestInitEvent instance.
     *
     * @var string
     */
    const INVEST_INIT = 'invest.init';

     /**
     * The invest.init.request event is thrown each time a user sends a request to the payment gateway
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRequestEvent instance.
     *
     * @var string
     */
    const INVEST_INIT_REQUEST = 'invest.init.request';

     /**
     * The invest.init.redirect event is thrown each time a user its been redirected to the payment gateway
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRequestEvent instance.
     *
     * @var string
     */
    const INVEST_INIT_REDIRECT = 'invest.init.redirect';

     /**
     * The invest.notify event is thrown each time the payment gateway reaches the notifyUrl to confirm the transaction
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRequestEvent instance.
     *
     * @var string
     */
    const INVEST_NOTIFY = 'invest.notify';

     /**
     * The invest.compelte event is thrown each time the payment gateway returns to a notifyUrl or returnUrl
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestInitEvent instance.
     *
     * @var string
     */
    const INVEST_COMPLETE = 'invest.complete';

     /**
     * The invest.complete.request event is thrown each time a user sends a request to the payment gateway to confirm the transaction
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRequestEvent instance.
     *
     * @var string
     */
    const INVEST_COMPLETE_REQUEST = 'invest.complete.request';

    /**
     * The invest.failed event is thrown each time a user fails to complete a payment process
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRequestEvent instance.
     *
     * @var string
     */
    const INVEST_FAILED = 'invest.failed';

    /**
     * The invest.success event is thrown each time a user completes a successful payment
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRequestEvent instance.
     *
     * @var string
     */
    const INVEST_SUCCEEDED = 'invest.success';

}
