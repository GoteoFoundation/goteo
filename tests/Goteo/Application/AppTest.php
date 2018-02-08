<?php


namespace Goteo\Application\Tests;

use Goteo\TestCase;

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection;

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

        App::setRoutes($this->createMock('Symfony\Component\Routing\RouteCollection'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', App::getRoutes());

        App::setRequest($this->createMock('Symfony\Component\HttpFoundation\Request'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', App::getRequest());

        App::setServiceContainer(new DependencyInjection\ContainerBuilder());
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', App::getServiceContainer());
    }

/*    public function testNotFound() {

        $app = $this->getFullApp();

        $response = $app->handle(Request::create('/non-existing', 'GET'));
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('</body>', $response->getContent());
    }

*/
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

/*
    public function testPublicRoutes() {
        $common = array('name="description"', 'property="og:title"', 'property="og:description"', '<title>', '<div id="wrapper"', '<div id="header"', '<div id="main"', '<div id="footer"', '<div id="sub-footer"', 'js/goteo.js"', 'css/goteo.css"', '</html>');
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
            $this->assertEquals(200, $response->getStatusCode(), "Failed route: [$route]" .$response->getContent());
            $this->assertContains('</body>', $response->getContent());
            foreach($parts + $common as $content) {
                $this->assertContains($content, $response->getContent(), "Failed route: [$route]");
            }
        }
    }
    */

    protected function getFullApp($exception = null)
    {
        App::clearApp();
        $routes = include( __DIR__ . '/../../../src/routes.php' );
        $container  = include( __DIR__ . '/../../../src/container.php' );
        App::setRoutes($routes);
        App::setServiceContainer($container);
        // set routes parameter
        App::getServiceContainer()->setParameter('routes', App::getRoutes());

        return App::get();
    }

}
