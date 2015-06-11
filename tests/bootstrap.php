<?php


use Goteo\Application\App;

//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/app/');

require_once __DIR__ . '/../src/autoload.php';

//ensures we have cache to test
define('SQL_CACHE_TIME', 1);

