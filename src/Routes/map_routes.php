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

$map = new RouteCollection();


//////////// map ROUTES ///////////////////

$map->add('map-channel', new Route(
    '/channel/{cid}',
    array(
        '_controller' => 'Goteo\Controller\MapController::channelAction'
    )
));


///// END map /////


return $map;
