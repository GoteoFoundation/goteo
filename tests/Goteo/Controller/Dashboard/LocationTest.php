<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Controller\Dashboard\Location;

class LocationTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Location();

        $this->assertInstanceOf('\Goteo\Controller\Dashboard\Location', $controller);

        return $controller;
    }
}
