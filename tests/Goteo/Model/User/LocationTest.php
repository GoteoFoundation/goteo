<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Location;

class LocationTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Location();

        $this->assertInstanceOf('\Goteo\Model\User\Location', $converter);

        return $converter;
    }
}
