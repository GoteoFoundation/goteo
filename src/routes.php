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
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerException;

use Goteo\Model\Node;
use Goteo\Model\Matcher;
use Goteo\Application\Exception\ModelNotFoundException;


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


if(Config::get('donate.landing')) {
    $routes->add('donate-landing', new Route(
        '/donate',
        array('_controller' => 'Goteo\Controller\DonateController::donateLandingAction')
    ));
}

if(Config::get('donate.dashboard')) {
    $donate_routes = include __DIR__ . '/Routes/donate_routes.php';
    $donate_routes->addPrefix('/donate');
    $routes->addCollection($donate_routes);
}

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

$project_routes = include __DIR__ . '/Routes/project_routes.php';
$project_routes->addPrefix('/project');
$routes->addCollection($project_routes);
$routes->add('widget-project-empty', new Route(
    '/widget/{id}',
    array('_controller' => function($id) {
        return new RedirectResponse("/widget/project/$id");
    })
));
$routes->add('widget-wof-empty', new Route(
    '/wof/{id}',
    array('_controller' => function($id) {
        return new RedirectResponse("/widget/wof/$id");
    })
));

$project_routes = include __DIR__ . '/Routes/widget_routes.php';
$project_routes->addPrefix('/widget');
$routes->addCollection($project_routes);

$auth_routes = include __DIR__ . '/Routes/auth_routes.php';
$routes->addCollection($auth_routes);

$routes->add('contact-form', new Route(
    '/contact',
    array(
        '_controller' => 'Goteo\Controller\ContactController::indexAction'
    )
));
$contact_routes = include __DIR__ . '/Routes/contact_routes.php';
$contact_routes->addPrefix('/contact');
$routes->addCollection($contact_routes);

$misc_routes = include __DIR__ . '/Routes/misc_routes.php';
$routes->addCollection($misc_routes);

$routes->add('blog', new Route(
    '/blog',
    array('_controller' => 'Goteo\Controller\BlogController::indexAction')
));

$routes->add('blog-section', new Route(
    '/blog-section/{section}',
    array(
        '_controller' => 'Goteo\Controller\BlogController::indexAction',
        'tag' => '' //optional parameter
    )
));

$routes->add('blog-tag', new Route(
    '/blog-tag/{tag}',
    array(
        '_controller' => 'Goteo\Controller\BlogController::indexAction',
        'section' => '' //optional parameter
    )
));

$routes->add('blog-post', new Route(
    '/blog/{slug}',
    array('_controller' => 'Goteo\Controller\BlogController::postAction')
));

$routes->add('rss', new Route(
    '/rss/{lang}',
    array(
        '_controller' => 'Goteo\Controller\RssController::indexAction',
        'lang' => '' //optional parameter
    )
));

$routes->add('newsletter', new Route(
    '/newsletter/{id}',
    array(
        '_controller' => 'Goteo\Controller\NewsletterController::indexAction',
        'id' => null //optional parameter
    )
));

$mail_routes = include __DIR__ . '/Routes/mail_routes.php';
$mail_routes->addPrefix('/mail');
$routes->addCollection($mail_routes);

$user_routes = include __DIR__ . '/Routes/user_routes.php';
$user_routes->addPrefix('/user');
$routes->addCollection($user_routes);

$routes->add('channel-list', new Route(
    '/channels',
    array('_controller' => 'Goteo\Controller\ChannelController::listChannelsAction')
));

$channel_routes = include __DIR__ . '/Routes/channel_routes.php';
$channel_routes->addPrefix('/channel');
$routes->addCollection($channel_routes);

$routes->add('matcher', new Route(
    '/matcher/{id}',
    array('_controller' => function($id) {
        try {
            $channel = Node::get($id);
        } catch (ModelNotFoundException $e) {
            $matcher = Matcher::get($id);
            return new RedirectResponse('/user/' . $matcher->owner);
        }
        return new RedirectResponse('/channel/' . $id);
    })
));

$routes->add('workshop-view', new Route(
    '/workshop/{id}',
    array('_controller' => 'Goteo\Controller\WorkshopController::indexAction',
        'id' => null
        )
));

$images_routes = include __DIR__ . '/Routes/images_routes.php';
$routes->addCollection($images_routes);

$routes->add('admin', new Route(
    '/admin',
    array('_controller' => 'Goteo\Controller\AdminController::indexAction',)
));
$admin_routes = include __DIR__ . '/Routes/admin_routes.php';
$admin_routes->addPrefix('/admin');
$routes->addCollection($admin_routes);

$routes->add('translate', new Route(
    '/translate',
    array('_controller' => 'Goteo\Controller\TranslateController::indexAction',)
));
$translate_routes = include __DIR__ . '/Routes/translate_routes.php';
$translate_routes->addPrefix('/translate');
$routes->addCollection($translate_routes);

$api_routes = include __DIR__ . '/Routes/api_routes.php';
$api_routes->addPrefix('/api');
$routes->addCollection($api_routes);
$api_charts_routes = include __DIR__ . '/Routes/api_charts_routes.php';
$api_charts_routes->addPrefix('/api');
$routes->addCollection($api_charts_routes);
$api_maps_routes = include __DIR__ . '/Routes/api_maps_routes.php';
$api_maps_routes->addPrefix('/api');
$routes->addCollection($api_maps_routes);

// Any route not handeled before in /api
$routes->add('api-any-route', new Route(
        '/api/{url}',
        array(
            '_controller' => function($url) {
                View::setTheme('JSON');
                throw new ControllerException("Route [$url] not found");
            }
        ),
        array('url' => '.*')
));

///////// REDIRECT "/" ENDING ROUTES ///////////////

$routes->add('remove-trailing-slash', new Route(
        '/{url}',
        array(
            '_controller' => 'Goteo\Controller\ErrorController::removeTrailingSlashAction',
        ),
        array('url' => '.*/$',)
));

////// REDIRECT "//" STARTING ROUTES
$routes->add('remove-starting-slash', new Route(
        '/{url}',
        array(
            '_controller' => 'Goteo\Controller\ErrorController::removeStartingSlashAction',
        ),
        array('url' => '[/]+.*',)
));

$routes->add('map', new Route(
    '/map',
    array(
        '_controller' => 'Goteo\Controller\MapController::mapAction'
    )
));

$routes->add('map-zoom-latlng', new Route(
    '/map/{zoom}/{latlng}',
    array(
        '_controller' => 'Goteo\Controller\MapController::exactMapAction'
    )
));

// $map_routes = include __DIR__ . '/Routes/map_routes.php';
// $map_routes->addPrefix('/map');
// $routes->addCollection($map_routes);


// Discover impact
$routes->add('impact-discover', new Route(
    '/impact-discover',
    array(
        '_controller' => 'Goteo\Controller\ImpactDiscoverController::indexAction'
    )
));

$routes->add('impact-discover-map', new Route(
    '/impact-discover/map',
    array(
        '_controller' => 'Goteo\Controller\ImpactDiscoverController::mapAction'
    )
));

$routes->add('impact-discover-mosaic', new Route(
    '/impact-discover/mosaic',
    array(
        '_controller' => 'Goteo\Controller\ImpactDiscoverController::mosaicAction'
    )
));


return $routes;
