<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Location;

class LocationTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $location = new Location();

        $this->assertInstanceOf('\Goteo\Model\User\Location', $location);

        return $location;
    }

    /**
     *
     * @depends testInstance
     */
    public function testDefaultValidation($location) {
        $this->assertFalse($location->validate());
        $this->assertFalse($location->save());
    }

    public function testAddUserEntry() {
        $data = array(
            'location' => 1,
            'user' => '_simulated_user_test_'
        );
        $location = new Location($data);
        $this->assertTrue($location->validate());
        $this->assertTrue($location->save());
        $place = Location::get($location->user);
        $this->assertEquals($place, $location->location);

        return $location;
    }
    /**
     * @depends  testAddUserEntry
     */
    public function testRemoveAddLocationEntry($location) {
        $this->assertTrue($location->delete());
        $location2 = Location::get($location->id);
        $this->assertNull($location2);
    }
}
