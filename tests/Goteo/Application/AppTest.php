<?php


namespace Goteo\Application\Tests;

use Goteo\TestCase;

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class AppTest extends TestCase {
    public function testApp() {
        $app = $this->getFullApp();
        $this->assertInstanceOf('Goteo\Application\App', $app);
        $this->assertInstanceOf('Goteo\Application\App', App::get());
        $this->assertInstanceOf('Goteo\Application\App', $this->getAttribute($app, '_app'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $this->getAttribute($app, '_routes'));
        $this->assertInstanceOf('Symfony\Component\Routing\Matcher\UrlMatcherInterface', $this->getAttribute($app, '_matcher'));
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', $this->getAttribute($app, '_dispatcher'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $this->getAttribute($app, '_request'));
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Controller\ControllerResolver', $this->getAttribute($app, '_resolver'));

        App::clearApp();
        $this->assertNull($this->getAttribute($app, '_routes'));
        $this->assertNull($this->getAttribute($app, '_matcher'));
        $this->assertNull($this->getAttribute($app, '_dispatcher'));
        $this->assertNull($this->getAttribute($app, '_app'));
        $this->assertNull($this->getAttribute($app, '_request'));
        $this->assertNull($this->getAttribute($app, '_resolver'));

        App::setDispatcher($this->getMock('Symfony\Component\EventDispatcher\EventDispatcher'));
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', App::getDispatcher());

        App::setRoutes($this->getMock('Symfony\Component\Routing\RouteCollection'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', App::getRoutes());

        App::setRequest($this->getMock('Symfony\Component\HttpFoundation\Request'));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', App::getRequest());

        App::setMatcher($this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface'));
        $this->assertInstanceOf('Symfony\Component\Routing\Matcher\UrlMatcherInterface', App::getMatcher());

        App::setResolver($this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolver'));
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Controller\ControllerResolver', App::getResolver());
    }

    // public function testAppMethods() {

    // }

    public function testNotFound() {

        App::setRequest(new Request());
        $app = $this->getAppForException(new ResourceNotFoundException());

        $response = $app->handle(App::getRequest());
        // die($response->getStatusCode().'');
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
        App::setRoutes(include( __DIR__ . '/../../../src/routes.php' ));
        return App::get();
    }

    protected function getAppForException($exception)
    {
        App::clearApp();
        App::setRoutes(include( __DIR__ . '/../../../src/routes.php' ));
        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception))
        ;

        App::setMatcher($matcher);
        return App::get();
    }

}
