<?php


namespace Goteo\Application\Tests;

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class AppTest extends \PHPUnit_Framework_TestCase {

    public function testNotFound() {

        App::setRequest(new Request());
        $app = $this->getAppForException(new ResourceNotFoundException());

        $this->assertInstanceOf('Goteo\Application\App', $app);
        $response = $app->handle(App::getRequest());
        // die($response->getStatusCode().'');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('</body>', $response->getContent());
    }

    public function testPublicRoutes() {
        $common = array('name="description"', 'property="og:title"', 'property="og:description"', '<title>', '<div id="wrapper"', '<div id="header"', '<div id="main"', '<div id="footer"', '<div id="sub-footer"', '<div id="press_banner"', 'js/goteo.js"', 'js/goteo.js"', 'css/goteo.css"');
        $routes = array(
            '/' => ['<body class="home', '<div class="widget'],
            '/discover' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/calls' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/popular' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/recent' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/success' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/outdate' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/archive' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            '/discover/view/fullfilled' => ['<body class="home', '<div id="sub-header">', '<div id="menu">', '<form method="get" action="/discover/results">'],
            // '/blog' => ['<body class="blog">', '<div id="sub-header-secondary'],
        );
        foreach($routes as $route => $parts) {
            App::clearApp();
            $app = App::get();
            $response = $app->handle(App::getRequest());
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertContains('</body>', $response->getContent());
            foreach($parts + $common as $content) {
                $this->assertContains($content, $response->getContent());
            }
        }
    }

    protected function getAppForException($exception)
    {
        App::clearApp();
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
