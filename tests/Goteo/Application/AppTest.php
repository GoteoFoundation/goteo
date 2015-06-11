<?php


namespace Goteo\Application\Tests;

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class AppTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $app = $this->getAppForException(new ResourceNotFoundException());

        // $response = $app->handle(new Request());
        // $this->assertEquals(404, $response->getStatusCode());

        die($response->getStatusCode().'');

        return $app;
    }

    // public function testNotFoundHandling()
    // {
    //     $framework = $this->getFrameworkForException(new ResourceNotFoundException());

    //     $response = $framework->handle(new Request());

    //     $this->assertEquals(404, $response->getStatusCode());
    // }

    protected function getAppForException($exception)
    {
        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception))
        ;

        App::setMatcher($matcher);
        $resolver = $this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');

        App::setResolver($resolver);
        return App::get();
    }

}
