<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing\RouteCollection;

class App extends HttpKernel\HttpKernel {
    static protected $_app;
    static protected $_request;
    static protected $_routes;
    static protected $_sc;
    static protected $_debug  = false;
    static protected $_errors = array();

    /**
     * Gets the current request, if not defined is created from globals (_POST, _GET, etc)
     * @return Request object
     */
    static public function getRequest() {
        if (!self::$_request) {
            self::setRequest(Request::createFromGlobals());
        }
        return self::$_request;
    }

    /**
     * Sets the request,
     * must be called before App::get() in order to set a request different from globals
     */
    static public function setRequest(Request $request) {
        self::$_request = $request;
        // Session::factory($request);
    }

    /**
     * Gets the service container for the app
     * @return RouteColletion object
     */
    static public function getServiceContainer() {
        if (!self::$_sc) {
            self::$_sc = include (__DIR__ .'/../../container.php');
        }
        return self::$_sc;
    }

    /**
     * Sets the service container for the app
     * Must be called befor App::get() in order to set a different service container
     */
    static public function setServiceContainer(ContainerBuilder $sc) {
        self::$_sc = $sc;
    }

    /**
     * Shortcut to check if some service is available
     * @param  string $service the id of the service
     * @return Object          the instance of the service
     */
    static public function isService($service) {
        return self::getServiceContainer()->has($service);
    }
    /**
     * Shortcut to obtain a particular service
     * @param  string $service the id of the service
     * @return Object          the instance of the service
     */
    static public function getService($service) {
        return self::getServiceContainer()->get($service);
    }

    /**
     * Dispatchs an event
     * Events can be handled by any suscriber
     * @param  string     $eventName event ID
     * @param  Event|null $event     Event object
     * @return Event                 the result object
     */
    static public function dispatch($eventName, Event $event = null) {
        return self::getService('dispatcher')->dispatch($eventName, $event);
    }

    /**
     * Gets the routes for the app
     * @return RouteColletion object
     */
    static public function getRoutes() {
        if (!self::$_routes) {
            self::$_routes = include (__DIR__ .'/../../routes.php');
        }
        return self::$_routes;
    }

    /**
     * Sets the routes for the app
     * Must be called befor App::get() in order to set a different sets of routes
     */
    static public function setRoutes(RouteCollection $routes) {
        self::$_routes = $routes;
    }

    /**
     * Creates a new instance of the App ready to run
     * This methods can be optionally called before this ::get() call:
     *     ::setRequest()
     *     ::setRoutes()
     *     ::setServiceContainer()
     * Next calls to this method will return the current instantatied App
     * @return App object
     */
    static public function get() {
        if (!self::$_app) {

            // Getting the request either from global or simulated
            $request = self::getRequest();

            // Additional constants (this should be removed some day)
            // if ssl enabled
            $SITE_URL = $request->getHttpHost();
            if (Config::get('ssl')) {
                define('SEC_URL', 'https://'.$SITE_URL);
                if ($request->isSecure() || Session::isLogged()) {
                    define('SITE_URL', 'http://'.$SITE_URL);
                } else {
                    define('SITE_URL', 'http://'.$SITE_URL);
                }
            } else {
                define('SEC_URL', 'http://'.$SITE_URL);
                define('SITE_URL', 'http://'.$SITE_URL);
            }
            // Setup request for views
            \Goteo\Util\Foil\Extension\GoteoCore::setRequest($request);

            $sc         = self::getServiceContainer();
            self::$_app = $sc->get('app');
        }
        return self::$_app;
    }

    /**
     * Enables debug mode witch does:
     *     - *.yml settings always read
     *     - A bottom html profiler tool will be displayed on the bottom of the page
     *     - SQL queries will be collected fo statistics
     *     - Html/php error will be shown
     * @param  boolean $enable If must or no be enabled (do it before call App::get())
     *                         A null value does nothing
     * @return boolean         Returns the current debug mode
     */
    static public function debug($enable = null) {
        if ($enable === true) {
            self::$_debug = true;
        }
        if ($enable === false) {
            self::$_debug = false;
        }
        return self::$_debug;
    }

    /**
     * Executes the App HttpKernel::handle() function and sends the response to the navigator
     * Script should die after this call
     */
    public function run() {

        $request  = self::getRequest();
        $response = self::$_app->handle($request);

        $response->send();

        self::$_app->terminate($request, $response);
    }

    /**
     * Resets the current app
     * @return [type] [description]
     */
    static public function clearApp() {
        self::$_app     = null;
        self::$_sc      = null;
        self::$_routes  = null;
        self::$_request = null;
    }
    /**
     * Retrieves current colletected errors
     * @return array array of errors
     */
    static public function getErrors() {
        return self::$_errors;
    }

    /**
     * Error handler function to collect whatever error that can be collected
     * For use with the set_error_handler() function
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {
            case E_USER_DEPRECATED:
                // some symfony deprecated errors...
                $type = 'user deprecated';
                // return;
            case E_WARNING:
                $type = 'warning';
            case E_USER_WARNING:
                $type = 'user warning';
            case E_STRICT:
                $type = 'strict standards';
            case E_NOTICE:
                $type = 'notice';
            case E_USER_NOTICE:
                $type  = 'user notice';
                $fatal = false;
                break;
            default:
                $type  = 'fatal error';
                $fatal = true;
                break;
        }
        $trace = array_reverse(debug_backtrace());
        $info  = '';
        array_pop($trace);
        $txtinfo = strtoupper($type).': \''.$errstr.'\' at '.$errfile.' '.$errline.':'."\n";
        foreach ($trace as $item)
        $txtinfo .= '  '.(isset($item['file'])?$item['file']:'<unknown file>').' '.(isset($item['line'])?$item['line']:'<unknown line>').' calling '.$item['function'].'()'."\n";
        if (php_sapi_name() == 'cli') {
            echo $txtinfo;
        } else {
            $info .= '<p class="error_backtrace">'."\n";
            $info .= '<span class="type '.$type.'">'.$type.'</span> \'<b>'.$errstr.'</b>\' at <b>'.$errfile.' '.$errline.'</b>:'."\n";
            $info .= '  <ol>'."\n";
            foreach ($trace as $item)
                $info .= '    <li><b>'.(isset($item['file'])?$item['file']:'<unknown file>').' '.(isset($item['line'])?$item['line']:'<unknown line>').'</b> calling '.$item['function'].'()</li>'."\n";
            $info .= '  </ol>'."\n";
            $info .= '</p>'."\n";
        }
        if (ini_get('log_errors')) {
            $items = array();
            foreach ($trace as $item)
                $items[] = (isset($item['file'])?$item['file']:'<unknown file>').' '.(isset($item['line'])?$item['line']:'<unknown line>').' calling '.$item['function'].'()';
            $message = strtoupper($type).': \''.$errstr.'\' at '.$errfile.' '.$errline.': '.join(' | ', $items);
            error_log($message);
        }

        self::$_errors["$errfile:$errline"] = $info;
        self::getService('logger')->err($txtinfo);

        if ($fatal) {
            // $code = \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR;
            // \Goteo\Application\View::addFolder(__DIR__ . '/../../../templates/default');
            // // views function registering
            // // TODO: custom template
            // die(\Goteo\Application\View::render('errors/internal', ['msg' => $errstr, 'code' => $code, 'info' => $info], $code));
        }
    }
}
