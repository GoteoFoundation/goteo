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

// AJAX search
$channel->add('channel-discover-ajax', new Route(
    '/{id}/discover/ajax',
    array('_controller' => 'Goteo\Controller\ChannelController::ajaxSearchAction')
));

$channel->add('channel-discover-projects', new Route(
    '/{id}/discover',
    array('_controller' => 'Goteo\Controller\ChannelController::discoverProjectsAction')
));

$channel->add('channel-discover-projects-filter', new Route(
    '/{id}/discover/{filter}',
    array('_controller' => 'Goteo\Controller\ChannelController::discoverProjectsAction')
));

$channel->add('channel-faq', new Route(
    '/{id}/faq/{type}',
    ['_controller' => 'Goteo\Controller\ChannelController::faqAction']
));


$channel->add('channel-list-projects', new Route(
    '/{id}/{type}/{category}',
    array('_controller' => 'Goteo\Controller\ChannelController::listProjectsAction',
        // 'type' => 'available',
        'category' => null
        )
));


return $channel;
