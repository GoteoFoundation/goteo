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

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_USER_DEPRECATED); // for symfony user deprecated errors
set_error_handler('\\Goteo\\Application\\App::errorHandler');

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

// Config file...
Config::load();

//Get from globals defaults
App::setRequest(Request::createFromGlobals());

// Get the app
$app = App::get();

// handle routes, flush buffer out
$app->run();

