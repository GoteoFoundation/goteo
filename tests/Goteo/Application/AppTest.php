<?php


namespace Goteo\Application;

use Goteo\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

class AppTest extends TestCase {
    public function testApp() {
        $app = $this->getFullApp();
        $this->assertInstanceOf(App::class, $app);
        $this->assertInstanceOf(App::class, App::get());
        $this->assertInstanceOf(App::class, $this->getAttribute($app, '_app'));
        $this->assertInstanceOf(RouteCollection::class, $this->getAttribute($app, '_routes'));
        $this->assertInstanceOf(ContainerBuilder::class, $this->getAttribute($app, 'serviceContainer'));
        $this->assertInstanceOf(Request::class, $this->getAttribute($app, '_request'));

        App::clearApp();
        $this->assertNull($this->getAttribute($app, '_routes'));
        $this->assertNull($this->getAttribute($app, '_app'));
        $this->assertNull($this->getAttribute($app, '_request'));
        $this->assertNull($this->getAttribute($app, 'serviceContainer'));

        App::setRoutes($this->createMock(RouteCollection::class));
        $this->assertInstanceOf(RouteCollection::class, App::getRoutes());

        App::setRequest($this->createMock(Request::class));
        $this->assertInstanceOf(Request::class, App::getRequest());

        App::setServiceContainer(new ContainerBuilder());
        $this->assertInstanceOf(ContainerBuilder::class, App::getServiceContainer());
    }

    public function testLegacyRedirection() {
        $route = '/non-existing/';

        $app = $this->getFullApp();
        $response = $app->handle(Request::create($route));

        $this->assertTrue($response->isRedirection());
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

    protected function getFullApp(): App
    {
        App::clearApp();
        $routes = include( __DIR__ . '/../../../src/routes.php' );
        $container  = include( __DIR__ . '/../../../src/container.php' );
        App::setRoutes($routes);
        App::setServiceContainer($container);
        App::getServiceContainer()->setParameter('routes', App::getRoutes());

        return App::get();
    }

}
