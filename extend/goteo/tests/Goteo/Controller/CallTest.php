<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Call;

class CallTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Call();

        $this->assertInstanceOf('\Goteo\Controller\Call', $controller);

        return $controller;
    }
}
