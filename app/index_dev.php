<?php

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\App;
use Goteo\Application\Config;

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
// if (isset($_SERVER['HTTP_CLIENT_IP'])
//     || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
//     || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
// ) {
//     header('HTTP/1.0 403 Forbidden');
//     exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
// }

// Error reporting
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

App::debug(true);

// Config file...
Config::loadFromYaml('settings.yml');

// Add the debug toolbar as a service
$sc = App::getServiceContainer();
$sc->register('listener.profiler', 'Goteo\Util\Profiler\EventListener\ProfilerListener');
// add to the dispatcher as a subscriber
$sc->getDefinition('dispatcher')->addMethodCall('addSubscriber', array(new Symfony\Component\DependencyInjection\Reference('listener.profiler')));

//Get from globals defaults
App::setRequest(Request::createFromGlobals());

// Get the app
$app = App::get();

// handle routes, flush buffer out
$app->run();

