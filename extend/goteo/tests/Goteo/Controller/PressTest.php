<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Press;

class PressTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Press();

        $this->assertInstanceOf('\Goteo\Controller\Press', $controller);

        return $controller;
    }
}
