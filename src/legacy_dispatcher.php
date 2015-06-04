<?php

use Goteo\Core\Resource;
use Goteo\Core\Error;
use Goteo\Core\Redirection;
use Goteo\Core\ACL;
use Goteo\Core\View;
use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Library\Text;

try {
    // Get URI without query string
    $uri = strtok($_SERVER['REQUEST_URI'], '?');

    // Get requested segments
    $segments = preg_split('!\s*/+\s*!', $uri, -1, PREG_SPLIT_NO_EMPTY);

    // Normalize URI
    $uri = '/' . implode('/', $segments);

    // Get controller name
    $controller = 'Index';
    if (!empty($segments) && is_array($segments)) {
        // Take first segment as controller
        $firstSegment = ucfirst(array_shift($segments));

        if (class_exists("Goteo\\Controller\\$firstSegment")) {
            $controller = $firstSegment;
        }
    }

    // Check permissions on requested URI
    if (!ACL::check($uri) && substr($uri, 0, 11) !== '/user/login') {
        //si es directorio data/cache se supone que es un archivo cache que no existe y que hay que generar
        if (strpos($uri, 'data/cache/') !== false && $segments && $segments[3]) {
            //simularemos la llamada al controlador img: img/XXXxXXX/imagen.jpg
            array_shift($segments);
            $segments[0] = 'img';
        } //si es un cron (ejecutandose) con los parámetros adecuados, no redireccionamos
        elseif ((strpos($uri, 'cron') !== false || strpos($uri, 'system') !== false) && strcmp($_GET[md5(CRON_PARAM)], md5(CRON_VALUE)) === 0) {
            define('CRON_EXEC', true);
        } else {
            //if page exists, throw redirection, make it 404 otherwise
            // die($controller."-$firstSegment");
            if($controller !== 'Index') {
                Message::info(Text::get('user-login-required-access'));
                throw new Redirection(SEC_URL.'/user/login/?return='.rawurlencode($uri));
            }
            throw new Error(Error::NOT_FOUND);

        }
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
        // print_r($segments);print_r($method);print_r($class);die;

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

            // Invoke method
            $result = $method->invokeArgs($instance, $segments);

            if ($result === null) {
                // Start output buffer
                ob_start();
                // Get buffer contents
                $result = ob_get_contents();
                ob_end_clean();
            }

            if ($result instanceof Resource\MIME) {
                $mime_type = $result->getMIME();
                header("Content-type: $mime_type");
                if ($mime_type == 'text/html') {
                    //renovar tiempo de sesion si es tipo html
                    Session::renew();
                }
            }

            //esto suele llamar a un metodo magic: __toString de la vista View
            if($result instanceOf View) {
                echo $result->render();
            }
            //Provisional mientras la transicion
            elseif($result instanceOf \Symfony\Component\HttpFoundation\Response) {
                return $result;
            }
            elseif($result instanceOf \Symfony\Component\HttpFoundation\RedirectResponse) {
                return $result;
            }
            else {
                echo $result;
            }

            // if ($mime_type == "text/html" && GOTEO_ENV != 'real') {
            if ($mime_type == "text/html") {
                if (defined('DEBUG_SQL_QUERIES')) {
                    echo '<div style="position:static;top:10px;left:10px;padding:10px;z-index:1000;background:rgba(255,255,255,0.6)">[<a href="#" onclick="$(this).parent().remove();return false;">cerrar</a>]<pre>';
                    echo '<b>Server IP:</b> '.$_SERVER['SERVER_ADDR'] . '<br>';
                    echo '<b>Client IP:</b> '.$_SERVER['REMOTE_ADDR'] . '<br>';
                    echo '<b>X-Forwarded-for:</b> '.$_SERVER['HTTP_X_FORWARDED_FOR'] . '<br>';
                    echo '<b>SQL STATS:</b><br> '.print_r(Goteo\Core\DB::getQueryStats(), 1);
                    echo '</pre></div>';
                }

                echo '<!-- legacy: '.(microtime(true) - Session::getStartTime() ) . 's -->';
            }
            //Farewell
            return;
        }

    } catch (\ReflectionException $e) {
        // esto tendría que notificar a \GOTEO_FAIL_MAIL
        throw new Error(Error::BAD_REQUEST, $e->getMessage());
    }
    throw new Error(Error::NOT_FOUND);

} catch (Redirection $redirection) {
    $url = $redirection->getURL();
    $code = $redirection->getCode();
    return new \Symfony\Component\HttpFoundation\RedirectResponse($url);
    // header("Location: {$url}");
    // exit;
}

