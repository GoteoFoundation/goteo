<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Discover;

class DiscoverTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Discover();

        $this->assertInstanceOf('\Goteo\Controller\Discover', $controller);

        return $controller;
    }
}
