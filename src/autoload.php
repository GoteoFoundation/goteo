<?php


// Helpers
require_once __DIR__ . '/../src/Goteo/Core/Helpers.php';

//Composer packages
$loader = require (__DIR__  . '/../vendor/autoload.php' );

\Goteo\Application\Config::setLoader($loader);

//Main path
define('GOTEO_PATH', realpath(dirname(__DIR__)) . '/');
//Log path
define('GOTEO_LOG_PATH', GOTEO_PATH . 'var/logs/');
//Uploads
define('GOTEO_DATA_PATH', GOTEO_PATH . 'var/data/');
//cache
define('GOTEO_CACHE_PATH', GOTEO_PATH . 'var/cache/');

error_reporting(E_ALL & ~E_USER_DEPRECATED); // for symfony user deprecated errors
set_error_handler('\\Goteo\\Application\\App::errorHandler');

