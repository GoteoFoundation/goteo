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

$channel = new RouteCollection();

///// CHANNELS /////
$channel->add('channel-project-create', new Route(
    '/{id}/create',
    array('_controller' => 'Goteo\Controller\ChannelController::createAction')
));

$channel->add('channel', new Route(
    '/{id}',
    array('_controller' => 'Goteo\Controller\ChannelController::indexAction')
));

$channel->add('channel-list-projects', new Route(
    '/{id}/{type}/{category}',
    array('_controller' => 'Goteo\Controller\ChannelController::listProjectsAction',
        // 'type' => 'available',
        'category' => null
        )
));

return $channel;
