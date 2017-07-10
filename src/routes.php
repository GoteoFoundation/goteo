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
use Goteo\Application\View;
use Symfony\Component\HttpFoundation\RedirectResponse;


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

////// INVEST //////
///
/// /project/project-name/invest should be the same as /invest/project-name
$routes->add('invest', new Route(
    '/invest/{project_id}',
    array('_controller' => 'Goteo\Controller\InvestController::selectRewardAction',
        )
));


/// This is for compatibility with old routes
$routes->add('invest-old-route', new Route(
    '/project/{project_id}/invest',
    array('_controller' => 'Goteo\Controller\InvestController::selectRewardAction',
        )
));
$routes->add('invest-select-payment', new Route(
    '/invest/{project_id}/payment',
    array('_controller' => 'Goteo\Controller\InvestController::selectPaymentMethodAction',
        )
));
// ¿ optional step ? may skipped by javascript ?
$routes->add('invest-show-form', new Route(
    '/invest/{project_id}/form',
    array('_controller' => 'Goteo\Controller\InvestController::paymentFormAction',
        )
));
// Notify URL for gateways that need it
$routes->add('invest-gateway-notify', new Route(
    '/invest/notify/{method}',
    array('_controller' => 'Goteo\Controller\InvestController::notifyPaymentAction',
        )
));
// Payment gateways returning points
$routes->add('invest-gateway-complete', new Route(
    '/invest/{project_id}/{invest_id}/complete',
    array('_controller' => 'Goteo\Controller\InvestController::completePaymentAction',
        )
));
$routes->add('invest-user-data', new Route(
    '/invest/{project_id}/{invest_id}',
    array('_controller' => 'Goteo\Controller\InvestController::userDataAction',
        ),
    array('invest_id' => '[0-9]+')
));
$routes->add('invest-share', new Route(
    '/invest/{project_id}/{invest_id}/share',
    array('_controller' => 'Goteo\Controller\InvestController::shareAction',
        )
));

$routes->add('invest-msg-support', new Route(
    '/invest/{project_id}/{invest_id}/support-msg',
    array('_controller' => 'Goteo\Controller\InvestController::supportMsgAction',
        )
));


////// Pool rechargue //////
///

// Pool invest main route
// Notify URL for gateways that need it
// will use the same as invest route
$routes->add('pool', new Route(
    '/pool',
    array('_controller' => 'Goteo\Controller\PoolController::selectAmountAction',
        )
));

$routes->add('pool-select-payment', new Route(
    '/pool/payment',
    array('_controller' => 'Goteo\Controller\PoolController::selectPaymentMethodAction',
        )
));

$routes->add('pool-show-form', new Route(
    '/pool/form',
    array('_controller' => 'Goteo\Controller\PoolController::paymentFormAction',
        )
));

// Payment gateways returning points
$routes->add('pool-invest-gateway-complete', new Route(
    '/pool/{invest_id}/complete',
    array('_controller' => 'Goteo\Controller\PoolController::completePaymentAction',
        )
));


$routes->add('pool-invest-user-data', new Route(
    '/pool/{invest_id}',
    array('_controller' => 'Goteo\Controller\PoolController::userDataAction',
        ),
    array('invest_id' => '[0-9]+')
));

$routes->add('pool-invest-share', new Route(
    '/pool/{invest_id}/share',
    array('_controller' => 'Goteo\Controller\PoolController::shareAction',
        )
));

// New dashboard

$routes->add('dashboard-wallet', new Route(
    '/dashboard/wallet',
    array('_controller' => 'Goteo\Controller\DashboardController::walletAction',
        )
));

$routes->add('dashboard-wallet-projects-suggestion', new Route(
    '/dashboard/wallet/projects-suggestion',
    array('_controller' => 'Goteo\Controller\DashboardController::projectsSuggestionAction',
        )
));

$routes->add('dashboard-projects-analytics', new Route(
    '/dashboard/projects/analytics',
    array('_controller' => 'Goteo\Controller\DashboardController::analyticsAction',
        )
));

$routes->add('dashboard-projects-shared-materials', new Route(
    '/dashboard/projects/shared-materials',
    array('_controller' => 'Goteo\Controller\DashboardController::sharedMaterialsAction',
        )
));

$routes->add('dashboard-projects-save-material-url', new Route(
    '/dashboard/projects/save-material-url',
    array('_controller' => 'Goteo\Controller\DashboardController::saveMaterialUrlAction',
        )
));

$routes->add('dashboard-projects-update-materials-table', new Route(
    '/dashboard/projects/update-materials-table',
    array('_controller' => 'Goteo\Controller\DashboardController::updateMaterialsTableAction',
        )
));

$routes->add('dashboard-projects-icon-licenses', new Route(
    '/dashboard/projects/icon-licenses',
    array('_controller' => 'Goteo\Controller\DashboardController::getLicensesIconAction',
        )
));

$routes->add('dashboard-projects-save-new-material', new Route(
    '/dashboard/projects/save-new-material',
    array('_controller' => 'Goteo\Controller\DashboardController::saveNewMaterialAction',
        )
));


// AUTH user routes
$routes->add('auth-login', new Route(
    '/login',
    array('_controller' => 'Goteo\Controller\AuthController::loginAction',
        )
));

// OAUTH user routes
$routes->add('outh-login', new Route(
    '/login/{provider}',
    array('_controller' => 'Goteo\Controller\AuthController::oauthAction',
        )
));

// old route compatibility
$routes->add('auth-login-old-route', new Route(
    '/user/login',
    array('_controller' => 'Goteo\Controller\AuthController::redirectLoginAction',
        )
));

// register
$routes->add('auth-signup', new Route(
    '/signup',
    array('_controller' => 'Goteo\Controller\AuthController::signupAction',
        )
));
// old route for compatibility
$routes->add('auth-signup-old-route', new Route(
    '/user/register',
    array('_controller' => 'Goteo\Controller\AuthController::signupAction')
));
// Oauth registering form
$routes->add('auth-oauth-signup', new Route(
    '/signup/oauth',
    array('_controller' => 'Goteo\Controller\AuthController::oauthSignupAction')
));
// old route compatibility
$routes->add('auth-oauth-signup-old-route', new Route(
    '/user/oauth_register',
    array('_controller' => 'Goteo\Controller\AuthController::oauthSignupAction')
));

//Logout
$routes->add('auth-logout', new Route(
    '/logout',
    array('_controller' => 'Goteo\Controller\AuthController::logoutAction')
));
// old route compatibility
$routes->add('auth-logout-old-route', new Route(
    '/user/logout',
    array('_controller' => 'Goteo\Controller\AuthController::logoutAction')
));

// password recovery
$routes->add('auth-password-recovery', new Route(
    '/password-recovery/{token}',
    array('_controller' => 'Goteo\Controller\AuthController::passwordRecoveryAction',
          'token' => '', //optional parameter
        )
));

// password reset
$routes->add('auth-password-reset', new Route(
    '/password-reset',
    array('_controller' => 'Goteo\Controller\AuthController::passwordResetAction',
        )
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

// Favourite project

$routes->add('project-favourite', new Route(
    '/project/favourite/{project_id}',
    array('_controller' => 'Goteo\Controller\ProjectController::favouriteAction')
));

// Delete Favourite project

$routes->add('project-delete-favourite', new Route(
    '/project/delete-favourite',
    array('_controller' => 'Goteo\Controller\ProjectController::DeletefavouriteAction')
));

// Calculate investors average

$routes->add('project-investors-required', new Route(
    '/project/investors-required',
    array('_controller' => 'Goteo\Controller\ProjectController::investorsRequiredAction',
        )
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

$routes->add('about-librejs', new Route(
    '/about/librejs',
    array(
        '_controller' => 'Goteo\Controller\AboutController::librejsAction',
        )
));

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

/// Last newsletter
$routes->add('newsletter', new Route(
    '/newsletter/{id}',
    array('_controller' => 'Goteo\Controller\NewsletterController::indexAction',
        'id' => null //optional parameter
        )
));

////// MAILING /////
$routes->add('mail-track', new Route(
    '/mail/track/{token}.gif',
    array('_controller' => 'Goteo\Controller\MailController::trackAction')
));
$routes->add('mail-link', new Route(
    '/mail/link/{id}',
    array('_controller' => 'Goteo\Controller\MailController::linkAction')
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

// $routes->add('user-login', new Route(
//     '/user/login',
//     array('_controller' => 'Goteo\Controller\UserController::loginAction')
// ));

// $routes->add('user-register', new Route(
//     '/user/register',
//     array('_controller' => 'Goteo\Controller\UserController::registerAction')
// ));

//Oauth registering
// $routes->add('user-oauth', new Route(
//     '/user/oauth',
//     array('_controller' => 'Goteo\Controller\UserController::oauthAction')
// // ));
// $routes->add('user-oauth-register', new Route(
//     '/user/oauth_register',
//     array('_controller' => 'Goteo\Controller\UserController::oauthRegisterAction')
// ));

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

//Additional user redirections
$routes->add('user-redirect', new Route(
    '/user/{id}/{show}',
    array(
        '_controller' => 'Goteo\Controller\UserController::indexAction',
        'id' => '', //optional parameters
        'show' => '' //optional parameters
        )
));

///// END USER /////


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

$routes->add('channel-list', new Route(
    '/channels',
    array('_controller' => 'Goteo\Controller\ChannelController::listChannelsAction')
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

//// TRANSLATE ////
$routes->add('translate', new Route(
    '/translate',
    array('_controller' => 'Goteo\Controller\TranslateController::indexAction',
        )
));
$routes->add('translate-list-texts', new Route(
    '/translate/texts',
    array('_controller' => 'Goteo\Controller\TranslateController::listTextAction',
        )
));
$routes->add('translate-list', new Route(
    '/translate/{zone}',
    array('_controller' => 'Goteo\Controller\TranslateController::listAction',
        )
));

$routes->add('translate-edit-texts', new Route(
    '/translate/texts/{id}',
    array('_controller' => 'Goteo\Controller\TranslateController::editTextAction',
        )
));

$routes->add('translate-edit', new Route(
    '/translate/{zone}/{id}',
    array('_controller' => 'Goteo\Controller\TranslateController::editAction',
        )
));

//Compatibility redirect for old links
$routes->add('translate-old-edit', new Route(
    '/translate/{zone}/edit/{id}',
    array('_controller' => function ($zone, $id) {
            return new RedirectResponse("/translate/$zone/$id");
        })
    )
);


////// MINI-API: Json controllers for ajax searching /////
$api_routes = include __DIR__ . '/routes_api.php';
$api_routes->addPrefix('/api');
$routes->addCollection($api_routes);

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

return $routes;
