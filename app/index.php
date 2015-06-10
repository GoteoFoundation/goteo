<?php

use Goteo\Application\App;
use Goteo\Application\Config;

use Symfony\Component\HttpFoundation\Request;


//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

//Get from globals defaults
$request = App::getRequest();

//si el parametro GET vale:
// 0 se muestra estadísticas de SQL, pero no los logs
// 1 se hace un log con las queries no cacheadas
// 2 se hace un log con las queries no cacheadas y también las cacheadas
if ($request->query->has('sqldebug') && !defined('DEBUG_SQL_QUERIES')) {
    define('DEBUG_SQL_QUERIES', intval($request->query->get('sqldebug')));
}

$app = App::get();

$app->run();

