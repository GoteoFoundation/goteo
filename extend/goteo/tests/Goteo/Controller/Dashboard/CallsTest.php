<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Controller\Dashboard\Calls;

class CallsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Calls();

        $this->assertInstanceOf('\Goteo\Controller\Dashboard\Calls', $controller);

        return $controller;
    }
}
