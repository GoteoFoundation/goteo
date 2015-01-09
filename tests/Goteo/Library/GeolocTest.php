<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Geoloc;

class GeolocTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Geoloc();

        $this->assertInstanceOf('\Goteo\Library\Geoloc', $converter);

        return $converter;
    }
}
