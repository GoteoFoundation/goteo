<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Goteo\Application\View;
use Goteo\Plates\Extension;

// views function registering
View::getEngine()->loadExtension(new Extension\TextUtils());
View::getEngine()->loadExtension(new Extension\Pages());

// Some defaults
View::getEngine()->addData(['title' => GOTEO_META_TITLE]);

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\Index::index')
));

return $routes;
