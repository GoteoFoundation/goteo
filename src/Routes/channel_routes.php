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
    '/{id}/faq',
    ['_controller' => 'Goteo\Controller\ChannelController::faqAction']
));

$channel->add('channel-faq-slug', new Route(
    '/{id}/faq/{slug}',
    ['_controller' => 'Goteo\Controller\ChannelController::faqSlugAction']
));


$channel->add('channel-resources', new Route(
    '/{id}/resources',
    ['_controller' => 'Goteo\Controller\ChannelController::resourcesAction']
));

$channel->add('channel-resources-category', new Route(
    '/{id}/resources/{slug}',
    ['_controller' => 'Goteo\Controller\ChannelController::resourcesAction']
));

$channel->add('channel-project-apply', new Route(
    '/{id}/apply/{pid}',
    array('_controller' => 'Goteo\Controller\ChannelController::applyAction')
));

$channel->add('channel-impact-discover', new Route(
    '/{id}/impact-discover/{view}',
    array('_controller' => 'Goteo\Controller\ChannelController::impactDiscoverAction')
));

$channel->add('channel-blog-post', new Route(
    '/{id}/blog/{slug}',
    ['_controller' => 'Goteo\Controller\ChannelController::blogPostAction']
));

$channel->add('channel-list-projects', new Route(
    '/{id}/{type}/{category}',
    array(
        '_controller' => 'Goteo\Controller\ChannelController::listProjectsAction',
        'category' => null
    )
));

return $channel;
