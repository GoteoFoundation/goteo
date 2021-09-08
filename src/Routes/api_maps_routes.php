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

$api = new RouteCollection();

$api->add('api-map-channel', new Route(
    '/map/channel/{cid}',
    array('_controller' => 'Goteo\Controller\Api\MapsApiController::channelAction',
        )
));

$api->add('api-map-matcher', new Route(
    '/map/matcher/{mid}',
    array('_controller' => 'Goteo\Controller\Api\MapsApiController::matcherAction',
        )
));


return $api;
