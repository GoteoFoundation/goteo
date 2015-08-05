<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\IndexController::indexAction')
));

//// DISCOVER /////

$routes->add('discover-results', new Route(
    '/discover/results/{category}/{name}',
    array('category' => null, // optional
          'name' => null, // optional
          '_controller' => 'Goteo\Controller\DiscoverController::resultsAction',
          )
));
$routes->add('discover-view', new Route(
    '/discover/view/{type}',
    array('type' => 'all', // default value
          '_controller' => 'Goteo\Controller\DiscoverController::viewAction',
          )
));

$routes->add('discover', new Route(
    '/discover',
    array('_controller' => 'Goteo\Controller\DiscoverController::indexAction')
));

///// GLOSSARY /////

$routes->add('glossary', new Route(
    '/glossary',
    array('_controller' => 'Goteo\Controller\GlossaryController::indexAction')
));

//// PROJECT /////
/// TODO: more methods... ///

$routes->add('project-edit', new Route(
    '/project/edit/{id}/{step}',
    array(
        '_controller' => 'Goteo\Controller\ProjectController::editAction',
        'step' => 'userProfile'
        )
));

$routes->add('project-create', new Route(
    '/project/create',
    array('_controller' => 'Goteo\Controller\ProjectController::createAction')
));

//TODO: quitar esta guarrada:
$routes->add('project-raw', new Route(
    '/project/raw/{id}',
    array('_controller' => 'Goteo\Controller\ProjectController::rawAction')
));

$routes->add('project-delete', new Route(
    '/project/delete/{id}',
    array('_controller' => 'Goteo\Controller\ProjectController::deleteAction')
));

$routes->add('project-sections', new Route(
    '/project/{id}/{show}/{post}',
    array('_controller' => 'Goteo\Controller\ProjectController::indexAction',
        'id' => null, //optional
        'show' => 'home', //default
        'post' => null //optional
        )
));

//// CONTACT ////

//new captcha
$routes->add('contact-captcha', new Route(
    '/contact/captcha',
    array(
        '_controller' => 'Goteo\Controller\ContactController::captchaAction'
        )
));
//contact form
$routes->add('contact-form', new Route(
    '/contact',
    array(
        '_controller' => 'Goteo\Controller\ContactController::indexAction'
        )
));
///// ABOUT /////

$routes->add('about-sections', new Route(
    '/about/{id}',
    array(
        '_controller' => 'Goteo\Controller\AboutController::indexAction',
        'id' => '' //optional
        )
));

// service
$routes->add('service', new Route(
    '/service/{id}',
    array('_controller' => 'Goteo\Controller\AboutController::indexAction')
));

///// BLOG //////

$routes->add('blog-post', new Route(
    '/blog/{post}',
    array('_controller' => 'Goteo\Controller\BlogController::indexAction',
        'post' => '' //optional parameter
        )
));

///// RSS //////

$routes->add('rss', new Route(
    '/rss/{lang}',
    array('_controller' => 'Goteo\Controller\RssController::indexAction',
        'lang' => '' //optional parameter
        )
));

////// MAILING /////
$routes->add('mail-track', new Route(
    '/mail/track/{token}.gif',
    array('_controller' => 'Goteo\Controller\MailController::trackAction')
));
$routes->add('mail-url', new Route(
    '/mail/url/{token}',
    array('_controller' => 'Goteo\Controller\MailController::urlAction')
));
$routes->add('mail-token', new Route(
    '/mail/{token}',
    array('_controller' => 'Goteo\Controller\MailController::indexAction')
));


//////////// USER ROUTES ///////////////////

$routes->add('user-login', new Route(
    '/user/login',
    array('_controller' => 'Goteo\Controller\UserController::loginAction')
));

$routes->add('user-register', new Route(
    '/user/register',
    array('_controller' => 'Goteo\Controller\UserController::registerAction')
));

//Oauth registering
$routes->add('user-oauth', new Route(
    '/user/oauth',
    array('_controller' => 'Goteo\Controller\UserController::oauthAction')
));
$routes->add('user-oauth-register', new Route(
    '/user/oauth_register',
    array('_controller' => 'Goteo\Controller\UserController::oauthRegisterAction')
));

$routes->add('user-profile', new Route(
    '/user/profile/{id}/{show}/{category}',
    array(
        '_controller' => 'Goteo\Controller\UserController::profileAction',
        'id' => '', //optional parameters
        'show' => 'profile', //optional parameters
        'category' => '', //optional parameters
    )
));

$routes->add('user-edit', new Route(
    '/user/edit',
    array('_controller' => 'Goteo\Controller\UserController::editAction')
));

//User recover
$routes->add('user-recover', new Route(
    '/user/recover/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::recoverAction',
        'token' => ''
        )
));

//User newsletter unsubscribing
//Mispelled
$routes->add('user-unsuscribe', new Route(
    '/user/unsuscribe/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::unsubscribeAction',
        'token' => ''
        )
));
$routes->add('user-unsubscribe', new Route(
    '/user/unsubscribe/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::unsubscribeAction',
        'token' => ''
        )
));

//User unsubscribing
$routes->add('user-leave', new Route(
    '/user/leave/{token}',
    array(
        '_controller' => 'Goteo\Controller\UserController::leaveAction',
        'token' => ''
        )
));

//User email changing
$routes->add('user-changeemail', new Route(
    '/user/changeemail/{token}',
    array('_controller' => 'Goteo\Controller\UserController::changeemailAction')
));

//User activation
$routes->add('user-activation', new Route(
    '/user/activate/{token}',
    array('_controller' => 'Goteo\Controller\UserController::activateAction')
));

//Logout
$routes->add('user-logout', new Route(
    '/user/logout',
    array('_controller' => 'Goteo\Controller\UserController::logoutAction')
));

//Additional user redirections
$routes->add('user-redirect', new Route(
    '/user/{id}/{show}',
    array(
        '_controller' => 'Goteo\Controller\UserController::indexAction',
        'id' => '', //optional parameters
        'show' => '' //optional parameters
        )
));

//// IMAGES ////
// Live resize
$routes->add('images', new Route(
    '/img/{params}/{filename}',
    array('_controller' => 'Goteo\Controller\ImageController::indexAction',
        'params' => '', //default
        'filename' => ''
        ),
    array(
        'filename' => '.*'
        )
));

//OLD routes: TODO remove url from views...
$routes->add('images-old', new Route(
    '/image/{id}/{width}/{height}/{crop}',
    array('_controller' => 'Goteo\Controller\ImageController::oldIndexAction',
        'width' => 200,
        'height' => 200,
        'crop' => false
        )
));

///// CHANNELS /////
$routes->add('channel-project-create', new Route(
    '/channel/{id}/create',
    array('_controller' => 'Goteo\Controller\ChannelController::createAction')
));

$routes->add('channel', new Route(
    '/channel/{id}',
    array('_controller' => 'Goteo\Controller\ChannelController::indexAction')
));

$routes->add('channel-list-projects', new Route(
    '/channel/{id}/{type}/{category}',
    array('_controller' => 'Goteo\Controller\ChannelController::listProjectsAction',
        // 'type' => 'available',
        'category' => null
        )
));



////// ADMIN //////
$routes->add('admin', new Route(
    '/admin',
    array('_controller' => 'Goteo\Controller\AdminController::indexAction',
        )
));
$routes->add('admin-action', new Route(
    '/admin/{option}/{action}/{id}/{subaction}',
    array('_controller' => 'Goteo\Controller\AdminController::optionAction',
        'action' => 'list',
        'id' => null,
        'subaction' => null
        )
));

//Admin subcontrollers added manually for legacy compatibility
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\AccountsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NodeSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NodesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TransnodesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\BannersSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\BlogSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CategoriesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CommonsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CriteriaSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\FaqSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\HomeSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\GlossarySubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\IconsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\LicensesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\MailingSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NewsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NewsletterSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\PagesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\ProjectsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\PromoteSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\RecentSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\ReviewsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\RewardsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\SentSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\SponsorsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TagsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TemplatesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TextsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TranslatesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\UsersSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\WordcountSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\WorthSubController');



///// END USER /////

///////// REDIRECT "/" ENDING ROUTES ///////////////

$routes->add('remove-trailing-slash', new Route(
        '/{url}',
        array(
            '_controller' => 'Goteo\Controller\ErrorController::removeTrailingSlashAction',
        ),
        array(
            'url' => '.*/$',
        )
));

////// REDIRECT "//" STARTING ROUTES
$routes->add('remove-starting-slash', new Route(
        '/{url}',
        array(
            '_controller' => 'Goteo\Controller\ErrorController::removeStartingSlashAction',
        ),
        array(
            'url' => '[/]+.*',
        )
));

/// LEGACY DISPATCHER ////

$routes->add('legacy-dispacher', new Route(
        '/{url}',
        array(
            '_controller' => 'Goteo\Controller\ErrorController::legacyControllerAction',
        ),
        array(
            'url' => '.*',
        )
));

//TODO IMPORTANTE: data/cache y cron

return $routes;
