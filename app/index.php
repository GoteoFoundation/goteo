<?php

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\App;
use Goteo\Application\Config;

ini_set('display_errors', 0);

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

// Config file...
Config::loadFromYaml('settings.yml');

//Get from globals defaults
App::setRequest(Request::createFromGlobals());

// Get the app
$app = App::get();

// handle routes, flush buffer out
$app->run();

