<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Goteo\Application\Config;

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\Index::' . (Config::isNode() ? 'indexNode' : 'index'))
));

$routes->add('discover-results', new Route(
    '/discover/results/{category}/{name}',
    array('category' => null,
          'name' => null,
          '_controller' => 'Goteo\Controller\Discover::results',
          )
));
$routes->add('discover-view', new Route(
    '/discover/view/{type}',
    array('type' => 'all',
          '_controller' => 'Goteo\Controller\Discover::view',
          )
));
$routes->add('discover-patron', new Route(
    '/discover/patron/{user}',
    array('user' => 'all',
          '_controller' => 'Goteo\Controller\Discover::patron')
));
$routes->add('discover-calls', new Route(
    '/discover/calls',
    array('_controller' => 'Goteo\Controller\DiscoverAddons::calls')
));
$routes->add('discover-call', new Route(
        '/discover/call',
    array('_controller' => 'Goteo\Controller\DiscoverAddons::call')
));
$routes->add('discover', new Route(
    '/discover',
    array('_controller' => 'Goteo\Controller\Discover::index')
));
$routes->add('glossary', new Route(
    '/glossary',
    array('_controller' => 'Goteo\Controller\Glossary::index')
));

$routes->add('project-sections', new Route(
    '/project/{id}/{show}',
    array('_controller' => 'Goteo\Controller\Project::index')
));

$routes->add('project', new Route(
    '/project/{id}',
    array('_controller' => 'Goteo\Controller\Project::index')
));

$routes->add('about', new Route(
    '/about',
    array('_controller' => 'Goteo\Controller\About::index')
));

$routes->add('service', new Route(
    '/service/{id}',
    array('_controller' => 'Goteo\Controller\About::index')
));

//TODO IMPORTANTE: data/cache y cron

return $routes;
