<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Location;

class LocationTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $location = new Location();

        $this->assertInstanceOf('\Goteo\Model\Location', $location);

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

    public function testAddLocationEntry($location) {
        $data = array(
            'city' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'country_code' => 'XX',
            'latitude' => '0.1234567890',
            'longitude' => '-0.1234567890',
            'valid' => 1
        );
        $location = new Location($data);
        $this->assertTrue($location->validate());
        $this->assertTrue($location->save());
        $location2 = Location::get($location->id);
        $this->assertEquals($location->latitude, $location2->latitude);
        $this->assertEquals($location->longitude, $location2->longitude);
        $this->assertEquals($location->location, $location2->location);
        $this->assertEquals($location->method, $location2->method);
        $this->assertEquals($location->region, $location2->region);
        $this->assertEquals($location->id, $location2->id);
        //
        return $location2;
    }
    /**
     * @depends  testAddLocationEntry
     */
    public function testRemoveAddLocationEntry($location) {
        $this->assertTrue($location->delete());
        $location2 = Location::get($location->id);
        $this->assertFalse($location2);
    }
}
