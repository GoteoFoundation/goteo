<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Locations;

class LocationsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Locations();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Locations', $controller);

        return $controller;
    }
}
