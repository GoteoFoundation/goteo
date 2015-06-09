<?php

namespace Goteo\Application;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

class App extends HttpKernel\HttpKernel
{
    static protected $app;
    static protected $request;
    static protected $debug = false;

    public function __construct(RouteCollection $routes, Request $request)
    {
        $context = new Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        $resolver = new HttpKernel\Controller\ControllerResolver();

        $dispatcher = new EventDispatcher();
        //Node configuration
        $dispatcher->addSubscriber(new EventListener\UrlListener($request));
        //Lang, cookies info, etc
        $dispatcher->addSubscriber(new EventListener\SessionListener($request));
        //Security ACL
        $dispatcher->addSubscriber(new EventListener\AclListener($request));
        //Routes
        $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
        //Control 404 and other errors
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener('Goteo\\Controller\\ErrorController::exceptionAction'));
        // Streamed responses
        // $dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
        //Automatic HTTP correct specifications
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

        //TODO: debug toolbar for queries
        //if (self::debug()) { ... }

        parent::__construct($dispatcher, $resolver);
    }

    static public function get() {
        if( ! self::$app ) {
            self::$request = Request::createFromGlobals();

            Config::loadFromYaml('settings.yml');
            // Routes
            $routes = Config::getRoutes();

            // Additional constants
            // si estamos en entorno seguro
            define('HTTPS_ON', self::$request->isSecure());
            // if ssl enabled
            $SITE_URL = self::$request->getHttpHost();
            if(Config::get('ssl')) {
                define('SEC_URL', 'https://' . $SITE_URL);
                if(self::$request->isSecure() || Session::isLogged()) {
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

            self::$app = new self($routes, self::$request);
        }
        return self::$app;
    }

    public function run() {

        $response = self::$app->handle(self::$request);

        $response->send();

        self::$app->terminate();
    }

    static public function debug($enable = null) {
        if($enable === true) {
            self::$debug = true;
        }
        if($enable === false) {
            self::$debug = false;
        }
        return self::$debug;
    }

}
