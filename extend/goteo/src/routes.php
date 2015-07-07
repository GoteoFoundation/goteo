<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\View;

// Old views custom folders
\Goteo\Core\View::addViewPath(__DIR__ . '/../views');

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
// chapucilla mientras no mejoramos el nodo barcelona con vistas nuevas
// también porque de momento foilphp no permite "prepend" de vistas
// Para testeo, barcelona.localhost sirve como nodo "barcelona"
//
// Añadir a /etc/hosts para testeo:
//
// 127.0.0.1 barcelona.localhost
//
if(in_array(strtok($_SERVER['HTTP_HOST'], '.'), array('barcelona', 'betabarcelona', 'euskadi', 'andalucia'))) {

    View::addFolder(__DIR__ . '/../templates/barcelona', 'barcelona');

    define('NODE_META_TITLE', 'Goteo Barcelona - Cofinanciació del procomú');

    Config::set('mail.contact', 'barcelona@goteo.org');
    Config::set('mail.contact_name', 'Goteo Barcelona');

    Config::set('current_node', 'barcelona');

    View::getEngine()->useData([
        'title' => 'Goteo Barcelona - Cofinanciació del procomú',
        'meta_description' => 'Xarxa social de finançament col·lectiu',
        'meta_keywords' => 'crowdfunding, procomún, commons, social, network, financiacion colectiva, cultural, creative commons, proyectos abiertos, open source, free software, licencias libres'
        ]);

    $custom_routes->add('subdomain-node', new Route(
        '/{url}',
        array(
            '_controller' => 'Goteo\Controller\NodeController::subdomainAction',
            'url' => '',
            ),
        array(
            'url' => '.*'
            )
    ));
}


//Discover addons
$custom_routes->add('discover-calls', new Route(
    '/discover/calls',
    array('_controller' => 'Goteo\Controller\DiscoverAddonsController::callsAction')
));
$custom_routes->add('discover-call', new Route(
        '/discover/call',
    array('_controller' => 'Goteo\Controller\DiscoverAddonsController::callAction')
));


// Calendar
$custom_routes->add('calendar', new Route(
    '/calendar',
    array('_controller' => 'Goteo\Controller\CalendarController::indexAction')
));

// Contracts
$custom_routes->add('contract-edit', new Route(
    '/contract/edit/{id}/{step}',
    array('_controller' => 'Goteo\Controller\ContractController::editAction',
        'step' => 'promoter'
        )
));

// Dirty method...
$custom_routes->add('contract-raw', new Route(
    '/contract/raw/{id}',
    array('_controller' => 'Goteo\Controller\ContractController::rawAction'
        )
));

// Download PDF
$custom_routes->add('contract-view', new Route(
    '/contract/{id}',
    array('_controller' => 'Goteo\Controller\ContractController::indexAction',
        'id' => null
        )
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
