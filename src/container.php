<?php

// example.com/src/container.php

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;

$sc = new DependencyInjection\ContainerBuilder();

// Context and matcher
$sc->register('context', 'Symfony\Component\Routing\RequestContext');
$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments(array('%routes%', new Reference('context')))
;

// resolver for the HttpKernel handle()
$sc->register('resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');

// Router for the dispatcher
$sc->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
    ->setArguments(array(new Reference('matcher')))
;

// always utf-8 output, just in case...
$sc->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
    ->setArguments(array('UTF-8'))
;

// Let's handle exceptions as 404 or 500 nice error pages
$sc->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
    ->setArguments(array('Goteo\\Controller\\ErrorController::exceptionAction'))
;

// Event Dispatcher object
$sc->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
    ->addMethodCall('addSubscriber', array(new Reference('listener.router')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.response')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.exception')))
;

// Goteo main app
$sc->register('app', 'Goteo\Application\App')
    ->setArguments(array(new Reference('dispatcher'), new Reference('resolver')))
;


return $sc;
