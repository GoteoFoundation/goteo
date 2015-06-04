<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Goteo\Application\Config;

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\Index::' . (Config::isNode() ? 'indexNode' : 'index'))
));

//// DISCOVER /////

$routes->add('discover-results', new Route(
    '/discover/results/{category}/{name}',
    array('category' => null,
          'name' => null,
          '_controller' => 'Goteo\Controller\Discover::resultsAction',
          )
));
$routes->add('discover-view', new Route(
    '/discover/view/{type}',
    array('type' => 'all',
          '_controller' => 'Goteo\Controller\Discover::viewAction',
          )
));
$routes->add('discover-patron', new Route(
    '/discover/patron/{user}',
    array('user' => 'all',
          '_controller' => 'Goteo\Controller\Discover::patronAction')
));
$routes->add('discover-calls', new Route(
    '/discover/calls',
    array('_controller' => 'Goteo\Controller\DiscoverAddons::callsAction')
));
$routes->add('discover-call', new Route(
        '/discover/call',
    array('_controller' => 'Goteo\Controller\DiscoverAddons::callAction')
));
$routes->add('discover', new Route(
    '/discover',
    array('_controller' => 'Goteo\Controller\Discover::indexAction')
));

///// GLOSSARY /////

$routes->add('glossary', new Route(
    '/glossary',
    array('_controller' => 'Goteo\Controller\Glossary::index')
));

$routes->add('project-edit', new Route(
    '/project/edit/{id}',
    array('_controller' => 'Goteo\Controller\Project::edit')
));

$routes->add('project-create', new Route(
    '/project/create',
    array('_controller' => 'Goteo\Controller\Project::create')
));

$routes->add('project-sections', new Route(
    '/project/{id}/{show}',
    array('_controller' => 'Goteo\Controller\Project::index')
));

$routes->add('project', new Route(
    '/project/{id}',
    array('_controller' => 'Goteo\Controller\Project::index')
));

$routes->add('about-sections', new Route(
    '/about/{id}',
    array('_controller' => 'Goteo\Controller\About::index')
));

$routes->add('about', new Route(
    '/about',
    array('_controller' => 'Goteo\Controller\About::index')
));

$routes->add('service', new Route(
    '/service/{id}',
    array('_controller' => 'Goteo\Controller\About::index')
));

$routes->add('blog-post', new Route(
    '/blog/{post}',
    array('_controller' => 'Goteo\Controller\Blog::index')
));
$routes->add('blog', new Route(
    '/blog',
    array('_controller' => 'Goteo\Controller\Blog::index')
));

//////////// USER ROUTES ///////////////////

$routes->add('user-login', new Route(
    '/user/login',
    array('_controller' => 'Goteo\Controller\User::loginAction')
));

$routes->add('user-register', new Route(
    '/user/register',
    array('_controller' => 'Goteo\Controller\User::registerAction')
));

//Oauth registering
$routes->add('user-oauth', new Route(
    '/user/oauth',
    array('_controller' => 'Goteo\Controller\User::oauthAction')
));
$routes->add('user-oauth-register', new Route(
    '/user/oauth_register',
    array('_controller' => 'Goteo\Controller\User::oauthRegisterAction')
));

$routes->add('user-profile', new Route(
    '/user/profile/{id}/{show}/{category}',
    array(
        '_controller' => 'Goteo\Controller\User::profileAction',
        'id' => '', //optional parameters
        'show' => 'profile', //optional parameters
        'category' => '', //optional parameters
    )
));

$routes->add('user-edit', new Route(
    '/user/edit',
    array('_controller' => 'Goteo\Controller\User::editAction')
));

//User recover
$routes->add('user-recover', new Route(
    '/user/recover/{token}',
    array(
        '_controller' => 'Goteo\Controller\User::recoverAction',
        'token' => ''
        )
));

//User newsletter unsubscribing
$routes->add('user-unsubscribe', new Route(
    '/user/unsuscribe/{token}',
    array(
        '_controller' => 'Goteo\Controller\User::unsubscribeAction',
        'token' => ''
        )
));

//User unsubscribing
$routes->add('user-leave', new Route(
    '/user/leave/{token}',
    array(
        '_controller' => 'Goteo\Controller\User::leaveAction',
        'token' => ''
        )
));

//User email changing
$routes->add('user-changeemail', new Route(
    '/user/changeemail/{token}',
    array('_controller' => 'Goteo\Controller\User::changeemailAction')
));

//User activation
$routes->add('user-activation', new Route(
    '/user/activate/{token}',
    array('_controller' => 'Goteo\Controller\User::activateAction')
));

//Logout
$routes->add('user-logout', new Route(
    '/user/logout',
    array('_controller' => 'Goteo\Controller\User::logoutAction')
));


//Additional user redirections
$routes->add('user-redirect', new Route(
    '/user/{id}/{show}',
    array(
        '_controller' => 'Goteo\Controller\User::indexAction',
        'show' => '' //optional parameter
        )
));

$routes->add('chanel', new Route(
    '/chanel/{id}',
    array('_controller' => 'Goteo\Controller\Chanel::indexAction')
));


///////// REDIRECT "/" ENDING ROUTES ///////////////

$routes->add('remove-trailing-slash', new Route(
        '/{url}',
        array(
            '_controller' => 'Goteo\Controller\RedirectingController::removeTrailingSlashAction',
        ),
        array(
            'url' => '.*/$',
        ),
        array(),
        '',
        array(),
        array('GET')
));

//TODO IMPORTANTE: data/cache y cron

return $routes;
