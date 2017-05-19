<?php

/*
 * This file is part of the Call addons Plugin Package For Goteo.
 * Adds the custom route /call/{call-id}/preview to preview projects in a call
 * and adds a http user/password authentication to access to it
 */


use Goteo\Application\App;
use Goteo\Application\Config;

use Symfony\Component\DependencyInjection\Reference;

// Autoload additional Classes
Config::addAutoloadDir(__DIR__ . '/src');

// Adding custom services to the service container:
$sc = App::getServiceContainer();

// Barcelona NODE Listener
$sc->register('goteo.listener.http_auth', 'Goteo\Application\EventListener\BasicAuthListener')
   ->setArguments(array(new Reference('logger')));

$sc->getDefinition('dispatcher')
   ->addMethodCall('addSubscriber', array(new Reference('goteo.listener.http_auth')));
