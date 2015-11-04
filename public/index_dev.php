<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\Debug;

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
// if (isset($_SERVER['HTTP_CLIENT_IP'])
//     || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
//     || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
// ) {
//     header('HTTP/1.0 403 Forbidden');
//     exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
// }


//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

// Error reporting
App::debug(true);
// Too much notices...
// error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_DEPRECATED);

//
// Bored? Try the hard way and fix some notices:
// Debug\Debug::enable();

set_error_handler('\\Goteo\\Application\\App::errorHandler');

// Config file...

Config::load(is_file(__DIR__ . '/../config/dev-settings.yml') ? 'dev-settings.yml' : 'settings.yml' );

// Add the debug toolbar as a service
$sc = App::getServiceContainer();

// Add a log level debug to stderr
$sc->register('logger.handler.debug', 'Monolog\Handler\StreamHandler')
    ->setArguments(array('php://stderr', Monolog\Logger::DEBUG))
;
$sc->getDefinition('logger')->addMethodCall('pushHandler', array(new Symfony\Component\DependencyInjection\Reference('logger.handler.debug')));

$sc->register('paylogger.handler.debug', 'Monolog\Handler\StreamHandler')
    ->setArguments(array('php://stderr', Monolog\Logger::DEBUG))
;
$sc->getDefinition('paylogger')->addMethodCall('pushHandler', array(new Symfony\Component\DependencyInjection\Reference('paylogger.handler.debug')));

//Get from globals defaults
App::setRequest(Request::createFromGlobals());

// Get the app
$app = App::get();

// handle routes, flush buffer out
$app->run();

