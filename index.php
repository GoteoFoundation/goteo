<?php

use Goteo\Core\Resource,
    Goteo\Core\Error,
    Goteo\Core\Redirection,
    Goteo\Core\ACL,
    Goteo\Core\NodeSys,
    Goteo\Library\Text,
    Goteo\Library\Message,
    Goteo\Library\Lang;

require_once 'config.php';
require_once 'core/common.php';

/*
 * Pagina de en mantenimiento
 */
if (GOTEO_MAINTENANCE === true && $_SERVER['REQUEST_URI'] != '/about/maintenance' 
     && !isset($_POST['Num_operacion'])
    ) {
    header('Location: /about/maintenance');
}

// Include path
//set_include_path(GOTEO_PATH . PATH_SEPARATOR . '.');

// Autoloader
spl_autoload_register(

    function ($cls) {

        $file = __DIR__ . '/' . implode('/', explode('\\', strtolower(substr($cls, 6)))) . '.php';
        $file = realpath($file);

        if ($file === false) {

            // Try in library
//            $file = __DIR__ . '/library/' . implode('/', explode('\\', strtolower(substr($cls, 6)))) . '.php';
            $file = __DIR__ . '/library/' . strtolower($cls) . '.php';
//            die($cls . ' - ' . $file); //Si uso Text::get(id) no lo pilla
        }

        if ($file !== false) {
            include $file;
        }

    }

);

// Error handler
set_error_handler (

    function ($errno, $errstr, $errfile, $errline, $errcontext) {
        // @todo Insert error into buffer
//        echo "Error:  {$errno}, {$errstr}, {$errfile}, {$errline}, {$errcontext}<br />";
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

);

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

/**
 * Sesión.
 */
session_name('goteo');
session_start();

// Get URI without query string
$uri = strtok($_SERVER['REQUEST_URI'], '?');

// Get requested segments
$segments = preg_split('!\s*/+\s*!', $uri, -1, \PREG_SPLIT_NO_EMPTY);

// Normalize URI
$uri = '/' . implode('/', $segments);

// set Lang (forzado para el cron y el admin)
$forceLang = (strpos($uri, 'cron') !== false || strpos($uri, 'admin') !== false) ? 'es' : null;
Lang::set($forceLang);

// cambiamos el locale
\setlocale(\LC_TIME, Lang::locale());

try {
    // Check permissions on requested URI
    if (!ACL::check($uri)) {
        Message::Info(Text::get('user-login-required-access'));

        //si es un cron (ejecutandose) con los parámetros adecuados, no redireccionamos
        if ((strpos($uri, 'cron') !== false || strpos($uri, 'system') !== false) && strcmp($_GET[md5(CRON_PARAM)], md5(CRON_VALUE)) === 0) {
            define('CRON_EXEC', true);
        } else {
            throw new Redirection("/user/login/?return=".rawurlencode($uri));
        }
    }

    // Get controller name
    if (!empty($segments) && class_exists("Goteo\\Controller\\{$segments[0]}")) {
        // Take first segment as controller
        $controller = array_shift($segments);
    } else {
        $controller = 'index';
    }

    // Continue
    try {

        $class = new ReflectionClass("Goteo\\Controller\\{$controller}");

        if (!empty($segments) && $class->hasMethod($segments[0])) {
            $method = array_shift($segments);
        } else {
            // Try default method
            $method = 'index';
        }

        // ReflectionMethod
        $method = $class->getMethod($method);

        // Number of params defined in method
        $numParams = $method->getNumberOfParameters();
        // Number of required params
        $reqParams = $method->getNumberOfRequiredParameters();
        // Given params
        $gvnParams = count($segments);

        if ($gvnParams >= $reqParams && (!($gvnParams > $numParams && $numParams <= $reqParams))) {

            // Try to instantiate
            $instance = $class->newInstance();

            // Start output buffer
            ob_start();

            // Invoke method
            $result = $method->invokeArgs($instance, $segments);

            if ($result === null) {
                // Get buffer contents
                $result = ob_get_contents();
            }

            ob_end_clean();

            if ($result instanceof Resource\MIME) {
                header("Content-type: {$result->getMIME()}");
            }

            echo $result;

            // Farewell
            die;

        }

    } catch (\ReflectionException $e) {}

    throw new Error(Error::NOT_FOUND);

} catch (Redirection $redirection) {
    $url = $redirection->getURL();
    $code = $redirection->getCode();
    header("Location: {$url}");

} catch (Error $error) {

    include "view/error.html.php";

} catch (Exception $exception) {

    // Default error (500)
    include "view/error.html.php";
}