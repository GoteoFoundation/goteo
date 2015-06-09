<?php


namespace Goteo\Application\Tests;

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class AppTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = App::get();

        $this->assertInstanceOf('\Goteo\Application\App', $ob);
        $this->assertInstanceOf('\Goteo\Application\App', App::get());

        return $ob;
    }

    // public function testNotFoundHandling()
    // {
    //     $framework = $this->getFrameworkForException(new ResourceNotFoundException());

    //     $response = $framework->handle(new Request());

    //     $this->assertEquals(404, $response->getStatusCode());
    // }

    // protected function getFrameworkForException($exception)
    // {
    //     $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
    //     $matcher
    //         ->expects($this->once())
    //         ->method('match')
    //         ->will($this->throwException($exception))
    //     ;
    //     $resolver = $this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');

    //     return new App($matcher, $resolver);
    // }

}
