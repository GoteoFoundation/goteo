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

$user = new RouteCollection();


//////////// USER ROUTES ///////////////////

// $user->add('user-login', new Route(
//     '/login',
//     array('_controller' => 'Goteo\Controller\UserController::loginAction')
// ));

// $user->add('user-register', new Route(
//     '/register',
//     array('_controller' => 'Goteo\Controller\UserController::registerAction')
// ));

//Oauth registering
// $user->add('user-oauth', new Route(
//     '/oauth',
//     array('_controller' => 'Goteo\Controller\UserController::oauthAction')
// // ));
// $user->add('user-oauth-register', new Route(
//     '/oauth_register',
//     array('_controller' => 'Goteo\Controller\UserController::oauthRegisterAction')
// ));

$user->add('user-profile', new Route(
    '/profile/{id}/{show}/{category}',
    array(
        '_controller' => 'Goteo\Controller\UserController::profileAction',
        'id' => '', //optional parameters
        'show' => 'profile', //optional parameters
        'category' => '', //optional parameters
    )
));

$user->add('user-edit', new Route(
    '/edit',
    array('_controller' => 'Goteo\Controller\UserController::editAction')
));

//User recover
$user->add('user-recover', new Route(
    '/recover/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::recoverAction',
        'token' => ''
        )
));

//User newsletter unsubscribing
//Mispelled
$user->add('user-unsuscribe', new Route(
    '/unsuscribe/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::unsubscribeAction',
        'token' => ''
        )
));

$user->add('user-unsubscribe', new Route(
    '/unsubscribe/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::unsubscribeAction',
        'token' => ''
        )
));

//User unsubscribing
$user->add('user-leave', new Route(
    '/leave/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::leaveAction',
        'token' => ''
        )
));

//User email changing
$user->add('user-changeemail', new Route(
    '/changeemail/{token}',
    array('_controller' => 'Goteo\Controller\UserController::changeemailAction')
));

//User activation
$user->add('user-activation', new Route(
    '/activate/{token}',
    array('_controller' => 'Goteo\Controller\UserController::activateAction')
));

//Additional user redirections
$user->add('user-redirect', new Route(
    '/{id}/{show}',
    array(
        '_controller' => 'Goteo\Controller\UserController::indexAction',
        'id' => '', //optional parameters
        'show' => '' //optional parameters
        )
));

///// END USER /////


return $user;
