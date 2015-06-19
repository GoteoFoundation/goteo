<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Goteo\Application\App;

$custom_routes = new RouteCollection();
$custom_routes->add('barcelona-node-redirection', new Route(
    '/channel/barcelona',
    array(
        '_controller' => function() {
                return new RedirectResponse('//barcelona.goteo.org');
            }
        )
    )
);

$custom_routes->add('barcelona-node', new Route(
    '/{url}',
    array(
        '_controller' => 'Goteo\Controller\NodeController::barcelonaAction',
        'url' => ''
        ),
    array(
        'url' => '.*',
         'domain' => 'barcelona.goteo.org|betabarcelona.goteo.org|devgoteo.org'
        ), // Para testeo, devgoteo.org sirve como nodo "barcelona"
    array(),
    '{domain}'
));

// Calendar
$custom_routes->add('calendar', new Route(
    '/calendar',
    array('_controller' => 'Goteo\Controller\CalendarController::indexAction')
));

// Adding Default routes
$main_routes = include(__DIR__ . '/../../../src/routes.php');
$custom_routes->addCollection($main_routes);

// Adding more admin subcontrollers

\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\BazarSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CallsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TranscallsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CampaignsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\InfoSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\InvestsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\OpenTagsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\PatronSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\ReportsSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\StoriesSubController');
\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TasksSubController');

return $custom_routes;
