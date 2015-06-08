<?php

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;


error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

$request = Request::createFromGlobals();

//si el parametro GET vale:
// 0 se muestra estadísticas de SQL, pero no los logs
// 1 se hace un log con las queries no cacheadas
// 2 se hace un log con las queries no cacheadas y también las cacheadas
if ($request->query->has('sqldebug') && !defined('DEBUG_SQL_QUERIES')) {
    define('DEBUG_SQL_QUERIES', intval($request->query->get('sqldebug')));
}

// Quitar legacy
if (!$request->query->has('no-legacy') && !defined('USE_LEGACY_DISPACHER')) {
    define('USE_LEGACY_DISPACHER', true);
}

$app = App::get();

$app->run();

