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

$route = new RouteCollection();

////// ADMIN //////
$route->add('widget-project', new Route(
    '/project/{id}',
    array('_controller' => 'Goteo\Controller\WidgetController::projectAction')
));

$route->add('widget-wof', new Route(
    '/wof/{id}',
    array('_controller' => 'Goteo\Controller\WidgetController::wofAction')
));

return $route;
