<?php


namespace Goteo\Application\Tests;

use Goteo\TestCase;

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;

class AppTest extends TestCase {
    public function testApp() {
        $app = $this->getFullApp();
        $this->assertInstanceOf('Goteo\Application\App', $app);
        $this->assertInstanceOf('Goteo\Application\App', App::get());
        $this->assertInstanceOf('Goteo\Application\App', $this->getAttribute($app, '_app'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $this->getAttribute($app, '_routes'));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $this->getAttribute($app, '_sc'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $this->getAttribute($app, '_request'));

        App::clearApp();
        $this->assertNull($this->getAttribute($app, '_routes'));
        $this->assertNull($this->getAttribute($app, '_app'));
        $this->assertNull($this->getAttribute($app, '_request'));
        $this->assertNull($this->getAttribute($app, '_sc'));

        App::setRoutes($this->getMock('Symfony\Component\Routing\RouteCollection'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', App::getRoutes());

        App::setRequest($this->getMock('Symfony\Component\HttpFoundation\Request'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', App::getRequest());

        App::setServiceContainer(new DependencyInjection\ContainerBuilder());
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', App::getServiceContainer());
    }

    public function testNotFound() {

        $app = $this->getAppForException(new ResourceNotFoundException());

        $response = $app->handle(new Request());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('</body>', $response->getContent());
    }

    public function testRedirections() {
        $routes = array(
            '/discover/',
            '/non-existing/'
        );
        foreach($routes as $route) {
            $app = $this->getFullApp();
            $response = $app->handle(Request::create($route, 'GET'));

            $this->assertTrue($response->isRedirection());
        }
    }

    public function testPublicRoutes() {
        $common = array('name="description"', 'property="og:title"', 'property="og:description"', '<title>', '<div id="wrapper"', '<div id="header"', '<div id="main"', '<div id="footer"', '<div id="sub-footer"', '<div id="press_banner"', 'js/goteo.js"', 'js/goteo.js"', 'css/goteo.css"');
        $routes = array(
            '/' => ['<body class="home', '<div class="widget'],
            '/discover' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            //TODO in extended
            // '/discover/calls' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/popular' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/recent' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/success' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/outdate' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/archive' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/fulfilled' => ['<body class="discover', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/blog' => ['<body class="blog">', '<div id="sub-header-secondary'],
        );
        foreach($routes as $route => $parts) {
            $app = $this->getFullApp();
            $response = $app->handle(Request::create($route, 'GET'));
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertContains('</body>', $response->getContent());
            foreach($parts + $common as $content) {
                $this->assertContains($content, $response->getContent());
            }
        }
    }

    protected function getFullApp($exception)
    {
        App::clearApp();
        //no extend configuration
        App::setRoutes(include( __DIR__ . '/../../../src/routes.php' ));
        App::setServiceContainer(include( __DIR__ . '/../../../src/container.php' ));
        return App::get();
    }

    protected function getAppForException($exception)
    {
        $sc = new DependencyInjection\ContainerBuilder();

        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
        // $matcher
        //     ->expects($this->once())
        //     ->method('match')
        //     ->will($this->throwException($exception))
        // ;
        $sc->register('context', 'Symfony\Component\Routing\RequestContext');
        $sc->register('matcher', $matcher);
        ;

        $sc->register('resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');
        $sc->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
            ->setArguments(array(new Reference('matcher')))
        ;
        $sc->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
            ->setArguments(array('UTF-8'))
        ;
        $sc->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
            ->setArguments(array('Goteo\\Controller\\ErrorController::exceptionAction'))
        ;
        $sc->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
            ->addMethodCall('addSubscriber', array(new Reference('listener.router')))
            ->addMethodCall('addSubscriber', array(new Reference('listener.response')))
            ->addMethodCall('addSubscriber', array(new Reference('listener.exception')))
        ;
        $sc->register('app', 'Goteo\Application\App')
            ->setArguments(array(new Reference('dispatcher'), new Reference('resolver')))
        ;

        return $sc->get('app');
    }

}
