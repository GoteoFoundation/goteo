<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Ws;

class WsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Ws();

        $this->assertInstanceOf('\Goteo\Controller\Ws', $controller);

        return $controller;
    }
}
