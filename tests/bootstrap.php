<?php


use Goteo\Application\App;
use Goteo\Application\Config;

//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/app/');

require_once __DIR__ . '/../src/autoload.php';

// error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_DEPRECATED);
// ini_set("display_errors", 0);

//ensures we have cache to test
define('SQL_CACHE_TIME', 1);

//TODO: to be deprecate
define('HTTPS_ON', false);
define('LANG', 'es');
define('SITE_URL', 'http://localhost');

// Config file...
Config::loadFromYaml('settings.yml');
