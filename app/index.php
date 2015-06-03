<?php

use Goteo\Core\Resource;
use Goteo\Core\Error;
use Goteo\Core\Redirection;
use Goteo\Core\ACL;
use Goteo\Core\Model;
use Goteo\Core\NodeSys;
use Goteo\Application\GoteoApp;
use Goteo\Application\View;
use Goteo\Application\Session;
use Goteo\Application\Cookie;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Library\Currency;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

/**
 * Sesión.
 */
Session::start('goteo-'.GOTEO_ENV, defined('GOTEO_SESSION_TIME') ? GOTEO_SESSION_TIME : 3600);
Session::onSessionExpires(function () {
    Message::Info(Text::get('session-expired'));
});
Session::onSessionDestroyed(function () {
    Message::Info('That\'s all folks!');
});

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

// if ssl enabled
$SSL = (defined('GOTEO_SSL') && GOTEO_SSL === true );

// segun sea nodo o central
$SITE_URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : GOTEO_URL;
$raw_url = str_replace('http:', '', $SITE_URL);

// SEC_URL (siempre https, si ssl activado)
define('SEC_URL', ($SSL) ? 'https:'.$raw_url : $SITE_URL);

// si estamos en entorno seguro
define('HTTPS_ON', ($request->server->get('HTTPS') === 'on' || $request->server->get('HTTP_X_FORWARDED_PROTO') === 'https' ));

// SITE_URL, según si estamos en entorno seguro o si el usuario esta autenticado
if ($SSL && (\HTTPS_ON || Session::isLogged())) {
    define('SITE_URL', SEC_URL);
} else {
    define('SITE_URL', $SITE_URL);
}

// si el usuario ya está validado debemos mantenerlo en entorno seguro
// usamos la funcionalidad de salto entre nodos para mantener la sesión
if ($SSL && Session::isLogged() && !\HTTPS_ON) {
    header('Location: ' . SEC_URL . $request->server->get('REQUEST_URI'));
    die;
}
/* Fin inicializacion constantes *_URL */

Lang::setDefault(GOTEO_DEFAULT_LANG);
Lang::setFromGlobals($request);

// set currency
Session::store('currency', Currency::set()); // depending on request

/* Cookie para la ley de cookies */
if (!Cookie::exists('goteo_cookies')) {
    Cookie::store('goteo_cookies', '1');
    Message::Info(Text::get('message-cookies'));
}
    // Message::Error('test error');

require_once __DIR__ . '/../src/defaults.php';

$routes = include __DIR__.'/../src/app.php';

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
$resolver = new HttpKernel\Controller\ControllerResolver();

$dispatcher = new EventDispatcher();
//Security ACL
$dispatcher->addSubscriber(new Goteo\Application\EventListener\AclListener($request));
//Routes
$dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
//Control 404 y legacy ControllerResolver
$dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener('Goteo\\Controller\\ErrorController::exceptionAction'));
//Automatic HTTP correct specifications
$dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

//TODO: auto clear messages subscriber
//TODO: debug toolbar for queries

$framework = new GoteoApp($dispatcher, $resolver);
$response = $framework->handle($request);

$response->send();
$framework->terminate();
