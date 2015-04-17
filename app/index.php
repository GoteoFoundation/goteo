<?php

use Goteo\Core\Resource;
use Goteo\Core\Error;
use Goteo\Core\Redirection;
use Goteo\Core\ACL;
use Goteo\Core\Model;
use Goteo\Core\NodeSys;
use Goteo\Application\View;
use Goteo\Application\Session;
use Goteo\Application\Cookie;
use Goteo\Application\Lang;
use Goteo\Library\Text;
use Goteo\Library\Message;
use Goteo\Library\Currency;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel;

//si el parametro GET vale:
// 0 se muestra estadísticas de SQL, pero no los logs
// 1 se hace un log con las queries no cacheadas
// 2 se hace un log con las queries no cacheadas y también las cacheadas
if (isset($_GET['sqldebug']) && !defined('DEBUG_SQL_QUERIES')) {
    define('DEBUG_SQL_QUERIES', intval($_GET['sqldebug']));
}

require_once __DIR__ . '/config.php';

//clean all caches if requested
if (isset($_GET['cleancache'])) {
    Model::cleanCache();
}

/*
 * Pagina de en mantenimiento
 */
if (GOTEO_MAINTENANCE === true && $_SERVER['REQUEST_URI'] != '/about/maintenance'
     && !isset($_POST['Num_operacion'])
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
$host = strtok($_SERVER['HTTP_HOST'], '.');

if (NodeSys::isValid($host)) {
    define('NODE_ID', $host);

} else {
    define('NODE_ID', GOTEO_NODE);
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
define('HTTPS_ON', ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ));

// SITE_URL, según si estamos en entorno seguro o si el usuario esta autenticado
if ($SSL && (\HTTPS_ON || Session::isLogged())) {
    define('SITE_URL', SEC_URL);
} else {
    define('SITE_URL', $SITE_URL);
}

// si el usuario ya está validado debemos mantenerlo en entorno seguro
// usamos la funcionalidad de salto entre nodos para mantener la sesión
if ($SSL && Session::isLogged() && !\HTTPS_ON) {
    header('Location: ' . SEC_URL . $_SERVER['REQUEST_URI']);
    die;
}
/* Fin inicializacion constantes *_URL */

Lang::setDefault(GOTEO_DEFAULT_LANG);
Lang::setFromGlobals();

// set currency
Session::store('currency', Currency::set()); // depending on request

/* Cookie para la ley de cookies */
if (!Cookie::exists('goteo_cookies')) {
    Cookie::store('goteo_cookies', '1');
    Message::Info(Text::get('message-cookies'));
}

/* Using the HTTP Foundation class */
$request = Request::createFromGlobals();
$routes = include __DIR__ . '/../src/app.php';

$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);
$resolver = new HttpKernel\Controller\ControllerResolver();

try {
    try {
        $request->attributes->add($matcher->match($request->getPathInfo()));

        $controller = $resolver->getController($request);
        $arguments = $resolver->getArguments($request, $controller);

        $response = call_user_func_array($controller, $arguments);
    } catch (ResourceNotFoundException $e) {
        //Try legacy controller
        try {
            include __DIR__ . '/../src/legacy_dispatcher.php';
        }
        catch(Error $e) {
            $response = new Response(View::render('errors/not_found', ['msg' => 'Not found', 'code' => $e->getCode()]), $e->getCode());
        }
        // $response = new Response(View::render('errors/not_found', ['msg' => 'Not found', 'code' => 404]), 404);
    }
    catch(LogicException $e) {
        $response = new Response(View::render('errors/not_found', ['msg' => $e->getMessage(), 'code' => 500]), 500);
    }
} catch (Exception $e) {
    $response = new Response(View::render('errors/default', ['msg' => $e->getMessage(), 'code' => 500]), 500);
    // $response = new Response($e->getMessage(), 500);
}

$response->send();
