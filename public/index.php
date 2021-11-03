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

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

// Create first the request object (to avoid other classes reading from php://input specially)
$request = Request::createFromGlobals();

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_DEPRECATED); // for symfony user deprecated errors

// Bored? Try the hard way and fix some notices:
//Symfony\Component\Debug\Debug::enable();
// error handle needs to go after autoload
set_error_handler('Goteo\Application\App::errorHandler');

$config = getenv('GOTEO_CONFIG_FILE');
if(!is_file($config)) $config = __DIR__ . '/../config/settings.yml';

Config::load($config);
Config::autosave();

if (Config::get('debug')) {
    ini_set('display_errors', 1);
    App::debug(true);
}

if (is_array(Config::get('proxies'))) {
    $request->setTrustedProxies(
        Config::get('proxies'),
        Request::HEADER_X_FORWARDED_ALL
    );
}

//Get from globals defaults
App::setRequest($request);

if (Config::get('debug')) {
    $handler = new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG);
    $handler->setFormatter(new Bramus\Monolog\Formatter\ColoredLineFormatter());

    App::getService('logger')->pushHandler($handler);
    App::getService('syslogger')->pushHandler($handler);
    App::getService('paylogger')->pushHandler($handler);
}

App::get()->run();
