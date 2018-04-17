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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Goteo\Application\View;
use Goteo\Application\Exception\ControllerException;


$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\IndexController::indexAction')
));

// Discover routes
$routes->add('discover', new Route(
    '/discover',
    array('_controller' => 'Goteo\Controller\DiscoverController::indexAction')
));
$discover_routes = include __DIR__ . '/Routes/discover_routes.php';
$discover_routes->addPrefix('/discover');
$routes->addCollection($discover_routes);

// Pool routes
$invest_routes = include __DIR__ . '/Routes/invest_routes.php';
$invest_routes->addPrefix('/invest');
$routes->addCollection($invest_routes);

// Pool routes
// Pool invest main route
// Notify URL for gateways that need it
// will use the same as invest route
$routes->add('pool', new Route(
    '/pool',
    array('_controller' => 'Goteo\Controller\PoolController::selectAmountAction',
        )
));
$pool_routes = include __DIR__ . '/Routes/pool_routes.php';
$pool_routes->addPrefix('/pool');
$routes->addCollection($pool_routes);

// New dashboard
$dash_routes = include __DIR__ . '/Routes/dashboard_routes.php';
$dash_routes->addPrefix('/dashboard');
$routes->addCollection($dash_routes);
// empty dashboard
$routes->add('dashboard-activity-empty', new Route(
    '/dashboard',
    array('_controller' => function() {
        return new RedirectResponse('/dashboard/activity');
    })
));

// Project view
$project_routes = include __DIR__ . '/Routes/project_routes.php';
$project_routes->addPrefix('/project');
$routes->addCollection($project_routes);
// default widget compatibility
$routes->add('widget-project-empty', new Route(
    '/widget/{id}',
    array('_controller' => function($id) {
        return new RedirectResponse("/widget/project/$id");
    })
));
// old wof compatibility
$routes->add('widget-wof-empty', new Route(
    '/wof/{id}',
    array('_controller' => function($id) {
        return new RedirectResponse("/widget/wof/$id");
    })
));

// Widgets
$project_routes = include __DIR__ . '/Routes/widget_routes.php';
$project_routes->addPrefix('/widget');
$routes->addCollection($project_routes);

// Auth routes (no prefix)
$auth_routes = include __DIR__ . '/Routes/auth_routes.php';
$routes->addCollection($auth_routes);

// Contact
//contact form
$routes->add('contact-form', new Route(
    '/contact',
    array(
        '_controller' => 'Goteo\Controller\ContactController::indexAction'
        )
));
$contact_routes = include __DIR__ . '/Routes/contact_routes.php';
$contact_routes->addPrefix('/contact');
$routes->addCollection($contact_routes);


// Misc routes (pages mostly, no prefix)
$misc_routes = include __DIR__ . '/Routes/misc_routes.php';
$routes->addCollection($misc_routes);

///// BLOG //////

$routes->add('blog', new Route(
    '/blog',
    array('_controller' => 'Goteo\Controller\BlogController::indexAction'
        )
));

$routes->add('blog-section', new Route(
    '/blog-section/{section}',
    array(  '_controller' => 'Goteo\Controller\BlogController::indexAction',
            'section' => '' //optional parameter
        )
));

$routes->add('blog-tag', new Route(
    '/blog-tag/{tag}',
    array(  '_controller' => 'Goteo\Controller\BlogController::indexAction',
            'tag' => '' //optional parameter
        )
));

$routes->add('blog-post', new Route(
    '/blog/{post}',
    array('_controller' => 'Goteo\Controller\BlogController::postAction',
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


// Mailing routes
$mail_routes = include __DIR__ . '/Routes/mail_routes.php';
$mail_routes->addPrefix('/mail');
$routes->addCollection($mail_routes);

// User stuff routes
$user_routes = include __DIR__ . '/Routes/user_routes.php';
$user_routes->addPrefix('/user');
$routes->addCollection($user_routes);

// Channel routes
$routes->add('channel-list', new Route(
    '/channels',
    array('_controller' => 'Goteo\Controller\ChannelController::listChannelsAction')
));

$channel_routes = include __DIR__ . '/Routes/channel_routes.php';
$channel_routes->addPrefix('/channel');
$routes->addCollection($channel_routes);

///// MATCHERS /////
$routes->add('matcher', new Route(
    '/matcher/{id}',
    array('_controller' => function($id) {
        // Temporary redirect to a channel with the same name
        return new RedirectResponse('/channel/' .$id);
    })
));


// Images processing (no prefix)
$images_routes = include __DIR__ . '/Routes/images_routes.php';
$routes->addCollection($images_routes);

// Admin routes
$routes->add('admin', new Route(
    '/admin',
    array('_controller' => 'Goteo\Controller\AdminController::indexAction',
        )
));
$admin_routes = include __DIR__ . '/Routes/admin_routes.php';
$admin_routes->addPrefix('/admin');
$routes->addCollection($admin_routes);

// Translator routes
$routes->add('translate', new Route(
    '/translate',
    array('_controller' => 'Goteo\Controller\TranslateController::indexAction',
        )
));
$translate_routes = include __DIR__ . '/Routes/translate_routes.php';
$translate_routes->addPrefix('/translate');
$routes->addCollection($translate_routes);

////// MINI-API: Json controllers for ajax searching /////
$api_routes = include __DIR__ . '/Routes/api_routes.php';
$api_routes->addPrefix('/api');
$routes->addCollection($api_routes);
$api_charts_routes = include __DIR__ . '/Routes/api_charts_routes.php';
$api_charts_routes->addPrefix('/api');
$routes->addCollection($api_charts_routes);

// Any route not handeled before in /api
$routes->add('api-any-route', new Route(
        '/api/{url}',
        array(
            '_controller' => function($url) {
                View::setTheme('JSON');
                throw new ControllerException("Route [$url] not found");
            }
        ),
        array(
            'url' => '.*'
        )
));


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
