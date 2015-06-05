<?php

use Goteo\Core\Model;
use Goteo\Core\NodeSys;
use Goteo\Application\GoteoApp;
use Symfony\Component\HttpFoundation\Request;


require_once __DIR__ . '/config.php';

/*
 * Using the HTTP Foundation class
 * http://symfony.com/doc/current/book/http_fundamentals.html
 */

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

//clean all caches if requested
if ($request->query->has('cleancache')) {
    Model::cleanCache();
}


/*
 * Pagina de en mantenimiento
 */
if (GOTEO_MAINTENANCE === true && $request->server->get('REQUEST_URI') != '/about/maintenance'
     && !$request->request->has('Num_operacion')
    ) {
    header('Location: /about/maintenance');
    die;
}



/* Sistema nodos */
// Get Node and check it
$host = strtok($request->server->get('HTTP_HOST'), '.');

if (NodeSys::isValid($host)) {
    define('NODE_ID', $host);

} else {
    define('NODE_ID', GOTEO_NODE);
    // define('NODE_ID', 'barcelona');
}

// configuracion estatica
$conf_file = 'nodesys/'.NODE_ID.'/config.php';
if (file_exists($conf_file)) {
    require_once $conf_file;
}
/* Fin inicializacion nodo */

/* Iniciación constantes *_URL */

// Verificar settings
if (defined('SITE_URL') || !defined('GOTEO_URL')) {
    die('En los settings hay que definir la constante GOTEO_URL en vez de SITE_URL.');
}


require_once __DIR__ . '/../src/defaults.php';

// Routes
$routes = include __DIR__.'/../src/app.php';


$framework = new GoteoApp($routes);

$response = $framework->handle($request);

$response->send();

$framework->terminate();
