<?php


namespace Goteo\Controller\Cron\Tests;

use Goteo\Controller\Cron\Geoloc;

class GeolocTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Geoloc();

        $this->assertInstanceOf('\Goteo\Controller\Cron\Geoloc', $controller);

        return $controller;
    }
}
