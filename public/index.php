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
use Symfony\Component\HttpFoundation\Request;

$isDebugEnv = getenv("DEBUG");

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

// Create first the request object (to avoid other classes reading from php://input specially)
$request = Request::createFromGlobals();

error_reporting(E_ALL & ~E_NOTICE & ~E_USER_DEPRECATED); // for symfony user deprecated errors

ini_set('display_errors', $isDebugEnv);
if ($isDebugEnv) {
    App::debug(true);
}

// Bored? Try the hard way and fix some notices:
//Symfony\Component\Debug\Debug::enable();
// error handle needs to go after autoload
set_error_handler('Goteo\Application\App::errorHandler');

$config = getenv('GOTEO_CONFIG_FILE');
if ($isDebugEnv) {
    if (!is_file($config)) $config = __DIR__ . '/../config/dev-settings.yml';
}
if(!is_file($config)) $config = __DIR__ . '/../config/settings.yml';
Config::load($config);
Config::autosave();

if (is_array(Config::get('proxies'))) {
    $request->setTrustedProxies(
        Config::get('proxies'),
        Request::HEADER_FORWARDED
    );
}

//Get from globals defaults
App::setRequest($request);

if ($isDebugEnv) {
    $handler = new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG);
    $handler->setFormatter(new Bramus\Monolog\Formatter\ColoredLineFormatter());

    // Add a log level debug to stderr
    App::getService('logger')->pushHandler($handler);
    App::getService('syslogger')->pushHandler($handler);
    App::getService('paylogger')->pushHandler($handler);
}

// Get the app
App::get()->run();
