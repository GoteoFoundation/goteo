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
    $controller = '';
    if (!empty($segments) && is_array($segments)) {
        // Take first segment as controller
        $firstSegment = ucfirst(array_shift($segments));

        if (class_exists("Goteo\\Controller\\$firstSegment")) {
            $controller = $firstSegment;
        }
    }
    if(empty($controller)) {
        throw new Error(Error::NOT_FOUND);
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
            }

            //esto suele llamar a un metodo magic: __toString de la vista View
            if($result instanceOf View) {
                echo $result->render();
            }
            //Provisional mientras la transicion
            elseif($result instanceOf \Symfony\Component\HttpFoundation\Response) {
                return $result;
            }
            else {
                echo $result;
            }

            if ($mime_type == "text/html") {
                echo '<!-- legacy: '.(microtime(true) - Session::getStartTime() ) . 's -->';
            }
            //Farewell
            return;
        }

    } catch (\ReflectionException $e) {
        // esto tendría que notificar a Config::getMail('fail')
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

