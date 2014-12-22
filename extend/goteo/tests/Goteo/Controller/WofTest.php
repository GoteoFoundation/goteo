<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Wof;

class WofTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Wof();

        $this->assertInstanceOf('\Goteo\Controller\Wof', $controller);

        return $controller;
    }
}
