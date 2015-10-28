<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

use Goteo\Application\App;
use Goteo\Application\Config;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\DependencyInjection\Reference;

// Autoload additional Classes
Config::addAutoloadDir(__DIR__ . '/src');

$sc = App::getServiceContainer();

// Adding toolbar as eventlistener
$sc->register('dev.listener.profiler', 'Goteodev\Profiler\EventListener\ProfilerListener');
$sc->getDefinition('dispatcher')->addMethodCall('addSubscriber', array(new Reference('dev.listener.profiler')));

// Adding grunt-livereload script
$sc->register('dev.listener.livereload', 'Goteodev\Application\EventListener\LiveReloadListener');
$sc->getDefinition('dispatcher')->addMethodCall('addSubscriber', array(new Reference('dev.listener.livereload')));


// Adding Routes:

$routes = App::getRoutes();

// $routes->add('goteo-dev-payment-dummy', new Route(
//     '/payment/dummy',
//     array('_controller' => 'Goteodev\Controller\DummyPaymentGatewayController::paymentAction')
// ));


// Adding payment method
\Goteo\Payment\Payment::addMethod('Goteodev\Payment\DummyPaymentMethod');
