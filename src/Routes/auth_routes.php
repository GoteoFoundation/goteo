<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$auth = new RouteCollection();

// AUTH user routes
$auth->add('auth-login', new Route(
    '/login',
    array('_controller' => 'Goteo\Controller\AuthController::loginAction',
        )
));

// OAUTH user routes
$auth->add('outh-login', new Route(
    '/login/{provider}',
    array('_controller' => 'Goteo\Controller\AuthController::oauthAction',
        )
));

// old route compatibility
$auth->add('auth-login-old-route', new Route(
    '/user/login',
    array('_controller' => 'Goteo\Controller\AuthController::redirectLoginAction',
        )
));

// register
$auth->add('auth-signup', new Route(
    '/signup',
    array('_controller' => 'Goteo\Controller\AuthController::signupAction',
        )
));
// old route for compatibility
$auth->add('auth-signup-old-route', new Route(
    '/user/register',
    array('_controller' => 'Goteo\Controller\AuthController::signupAction')
));

// Oauth registering form
$auth->add('auth-oauth-signup', new Route(
    '/signup/oauth',
    array('_controller' => 'Goteo\Controller\AuthController::oauthSignupAction')
));
// old route compatibility
$auth->add('auth-oauth-signup-old-route', new Route(
    '/user/oauth_register',
    array('_controller' => 'Goteo\Controller\AuthController::oauthSignupAction')
));

//Logout
$auth->add('auth-logout', new Route(
    '/logout',
    array('_controller' => 'Goteo\Controller\AuthController::logoutAction')
));

// old route compatibility
$auth->add('auth-logout-old-route', new Route(
    '/user/logout',
    array('_controller' => 'Goteo\Controller\AuthController::logoutAction')
));

// password recovery
$auth->add('auth-password-recovery', new Route(
    '/password-recovery/{token}',
    array('_controller' => 'Goteo\Controller\AuthController::passwordRecoveryAction',
          'token' => '', //optional parameter
        )
));

// Old route compatibility
$auth->add('auth-old-password-recovery', new Route(
    '/user/recover',
    array('_controller' => 'Goteo\Controller\AuthController::passwordRecoveryAction',
        )
));

// password reset
$auth->add('auth-password-reset', new Route(
    '/password-reset',
    array('_controller' => 'Goteo\Controller\AuthController::passwordResetAction',
        )
));

return $auth;
