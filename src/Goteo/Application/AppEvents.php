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
     * The view.render event is thrown each time a Goteo\Application\View is rendered
     * it allows to add or change completely the view
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterViewEvent instance.
     *
     * @var string
     */
    const VIEW_RENDER = 'view.render';

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
     * The auth.resetpassword event is thrown each time a user reset their password
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterAuthEvent instance.
     *
     * @var string
     */
    const RESET_PASSWORD = 'auth.reset_password';

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

    /**
     * The invest.finished event is thrown each time a user completes a successful personal data entry
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestFinishEvent instance.
     *
     * @var string
     */
    const INVEST_FINISHED = 'invest.finished';

    /**
     * The invest.refund event is thrown by the Payment\Method\AbstractPaymentMethod when the refund method is called
     * Any attached listener can abort refunds due any particular reason by throwing an PaymentException Exception
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestEvent instance.
     *
     * @var string
     */
    const INVEST_REFUND = 'invest.refund';

    /**
     * The invest.refund.cancel event is thrown each time a payments processes a refund
     * for any reason others than the project related is not archived/failed
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_CANCELLED = 'invest.refund.cancel';

    /**
     * The invest.refund.cancel.failed event is thrown when manual refund it's done (normally a admin call)
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_CANCEL_FAILED = 'invest.refund.cancel.failed';

    /**
     * The invest.refund.return event is thrown each time a payments processes a refund
     * due the project related is archived/failed
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_RETURNED = 'invest.refund.return';

    /**
     * The invest.refund.failed event is thrown a refund process
     * due the project related is archived/failed fails
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_RETURN_FAILED = 'invest.refund.failed';

    /**
     * The invest.modify event is thrown when a Invest changes in some way not related to the money (ie: change owner)
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterInvestRefundEvent instance.
     *
     * @var string
     */
    const INVEST_MODIFY = 'invest.modify';

   /**
     * The project.created event is thrown when a project is created
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_CREATED = 'project.created';

   /**
     * The project.publish event is thrown when a project is in a REVIEW status and has to be published (manually)
     *
     * The event listener receives an
     * Goteo\Console\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_PUBLISH = 'project.publish';

    /**
     * The project.ready event is thrown when a project is in a EDIT status and has to be changed to REVIEW
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterProjectEvent instance.
     *
     * @var string
     */
    const PROJECT_READY = 'project.ready';

    /**
     * The project.post event is thrown when a project edits a post
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterProjectPostEvent instance.
     *
     * @var string
     */
    const PROJECT_POST = 'project.post';

    /**
     * The message.created event is thrown when a new message/comments is created
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterMessageEvent instance.
     *
     * @var string
     */
    const MESSAGE_CREATED = 'message.created';

    /**
     * The message.updated event is thrown when a new message/comments is updated
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterMessageEvent instance.
     *
     * @var string
     */
    const MESSAGE_UPDATED = 'message.updated';

    /**
     * The matcher.project event is thrown when a project is add to a Matcher or its status changes
     *
     * The event listener receives an
     * Goteo\Application\Event\FilterMatcherProjectEvent instance.
     *
     * @var string
     */
    const MATCHER_PROJECT = 'matcher.project';


}
