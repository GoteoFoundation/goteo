<?php

namespace Goteo\Application;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

class App extends HttpKernel\HttpKernel
{
    static protected $_app;
    static protected $_dispatcher;
    static protected $_request;
    static protected $_routes;
    static protected $_debug = false;
    static protected $_errors = array();


    public function __construct()
    {
        $request = self::getRequest();
        $routes = self::getRoutes();
        $context = new Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        $resolver = new HttpKernel\Controller\ControllerResolver();

        $dispatcher = self::getDispatcher();
        //Node configuration
        $dispatcher->addSubscriber(new EventListener\UrlListener());
        //Lang, cookies info, etc
        $dispatcher->addSubscriber(new EventListener\SessionListener());
        //Security ACL
        $dispatcher->addSubscriber(new EventListener\AclListener());
        //Routes
        $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
        //Control 404 and other errors
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener('Goteo\\Controller\\ErrorController::exceptionAction'));
        // Streamed responses
        // $dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
        //Automatic HTTP correct specifications
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

        //debug toolbar for queries and errors
        if (self::debug()) {
            $dispatcher->addSubscriber(new \Goteo\Util\Profiler\EventListener\ProfilerListener());
        }

        parent::__construct($dispatcher, $resolver);
    }

    static public function getDispatcher() {
        if( ! self::$_dispatcher) {
            self::$_dispatcher = new EventDispatcher();
        }
        return self::$_dispatcher;
    }

    static public function getRequest() {
        if( ! self::$_request) {
            self::$_request = Request::createFromGlobals();
        }
        return self::$_request;
    }

    static public function setRequest(Request $request) {
        self::$_request = $request;
    }

    static public function getRoutes() {
        if( ! self::$_routes ) {
            self::$_routes = include( __DIR__ . '/../../app.php' );
        }
        return self::$_routes;
    }

    static public function setRoutes(RouteCollection $routes) {
        self::$_routes = $routes;
    }

    static public function get() {
        if( ! self::$_app ) {
            $request = self::getRequest();

            // TODO: configurable file...
            Config::loadFromYaml('settings.yml');

            // Routes
            $routes = self::getRoutes();

            // Additional constants
            // si estamos en entorno seguro
            define('HTTPS_ON', $request->isSecure());
            // if ssl enabled
            $SITE_URL = $request->getHttpHost();
            if(Config::get('ssl')) {
                define('SEC_URL', 'https://' . $SITE_URL);
                if($request->isSecure() || Session::isLogged()) {
                    define('SITE_URL', 'http://' . $SITE_URL);
                }
                else {
                    define('SITE_URL', 'http://' . $SITE_URL);
                }
            }
            else {
                define('SEC_URL', 'http://' . $SITE_URL);
                define('SITE_URL', 'http://' . $SITE_URL);
            }

            self::$_app = new self($routes);
        }
        return self::$_app;
    }

    static public function debug($enable = null) {
        if($enable === true) {
            self::$_debug = true;
        }
        if($enable === false) {
            self::$_debug = false;
        }
        return self::$_debug;
    }

    public function run() {

        $response = self::$_app->handle(self::$_request);

        $response->send();

        self::$_app->terminate(self::$_request, $response);
    }


    static public function getErrors() {
        return self::$_errors;
    }

    static public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        if(self::debug()) {
            if(!(error_reporting() & $errno))
                return;
            switch($errno) {
                case E_USER_DEPRECATED  :
                    // some symfony deprecated errors...
                    return;
                case E_WARNING      :
                case E_USER_WARNING :
                case E_STRICT       :
                case E_NOTICE       :
                case E_USER_NOTICE  :
                    $type = 'warning';
                    $fatal = false;
                    break;
                default             :
                    $type = 'fatal error';
                    $fatal = true;
                    break;
            }
            $trace = array_reverse(debug_backtrace());
            $info = '';
            array_pop($trace);
            if(php_sapi_name() == 'cli') {
                echo 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
                foreach($trace as $item)
                    echo '  ' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()' . "\n";
            } else {
                $info .= '<p class="error_backtrace">' . "\n";
                $info .= '  Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
                $info .= '  <ol>' . "\n";
                foreach($trace as $item)
                    $info .= '    <li>' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()</li>' . "\n";
                $info .= '  </ol>' . "\n";
                $info .= '</p>' . "\n";
            }
            if(ini_get('log_errors')) {
                $items = array();
                foreach($trace as $item)
                    $items[] = (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()';
                $message = 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ': ' . join(' | ', $items);
                error_log($message);
            }
            self::$_errors["$errfile:$errline"] = $info;
            if($fatal) {
                $code = \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR;
                \Goteo\Application\View::addFolder(__DIR__ . '/../../../templates/default');
                // views function registering
                // TODO: custom template
                die(\Goteo\Application\View::render('errors/internal', ['msg' => $errstr, 'code' => $code, 'info' => $info], $code));
            }
        }
    }
}
