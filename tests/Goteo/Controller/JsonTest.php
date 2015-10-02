<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Json;

class JsonTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Json();

        $this->assertInstanceOf('\Goteo\Controller\Json', $controller);

        return $controller;
    }
}
