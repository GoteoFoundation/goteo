<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Location;

class LocationTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Location();

        $this->assertInstanceOf('\Goteo\Model\Location', $converter);

        return $converter;
    }
}
