<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\Debug;
use Symfony\Component\HttpFoundation\Request;

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
define('GOTEO_WEB_PATH', __DIR__ .'/');

require_once __DIR__ .'/../src/autoload.php';

// Create first the request object (to avoid other classes reading from php://input specially)
$request = Request::createFromGlobals();

// Error reporting
App::debug(true);
// Too much notices...
// error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_DEPRECATED); // for symfony user deprecated errors

//
// Bored? Try the hard way and fix some notices:
// Debug\Debug::enable();
// error handle needs to go after autoload
set_error_handler('Goteo\Application\App::errorHandler');

// Config file...
$config = getenv('GOTEO_CONFIG_FILE');
if(!is_file($config)) $config = __DIR__ . '/../config/dev-settings.yml';
if(!is_file($config)) $config = __DIR__ . '/../config/settings.yml';
Config::load($config);

//Get from globals defaults
App::setRequest($request);

$handler = new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG);
$handler->setFormatter(new Bramus\Monolog\Formatter\ColoredLineFormatter());

// Add a log level debug to stderr
App::getService('logger')->pushHandler($handler);
App::getService('syslogger')->pushHandler($handler);
App::getService('paylogger')->pushHandler($handler);

// Get the app
App::get()->run();
