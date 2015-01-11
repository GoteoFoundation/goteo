<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\UserLocation;
use Goteo\Model\Location;

class LocationTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $location = new UserLocation();

        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $location);

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

    public function testAddUserLocation() {
        $data = array(
            'location' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'lat' => '0.1234567890',
            'lon' => '-0.1234567890',
            'valid' => 1,
            'method' => 'simulated',
            'user' => '_simulated_user_test_'
        );
        $location = UserLocation::addUserLocation($data);
        $this->assertInstanceOf('\Goteo\Model\Location', $location);
        $location2 = Location::get($location->id);
        $this->assertInstanceOf('\Goteo\Model\Location', $location2);
        $this->assertEquals($location->lat, $location2->lat);
        $this->assertEquals($location->lon, $location2->lon);
        $this->assertEquals($location->location, $location2->location);
        $this->assertEquals($location->method, $location2->method);
        $this->assertEquals($location->region, $location2->region);
        $this->assertEquals($location->id, $location2->id);
        return $location2;
    }

    /**
     * @depends testAddUserLocation
     */
    public function testAddUserEntry($location) {
        $data = array(
            'location' => $location->id,
            'user' => '_simulated_user_test_'
        );
        $user_location = new UserLocation($data);
        $this->assertTrue($user_location->validate($errors), print_r($errors, 1));
        $this->assertTrue($user_location->save());
        $user_location2 = UserLocation::get($user_location->user);
        $this->assertEquals($user_location->location, $user_location2->location);
        $this->assertInternalType('array', $user_location2->locations);
        foreach($user_location2->locations as $loc) {
            $this->assertInstanceOf('\Goteo\Model\Location', $loc);
        }

        return $user_location2;
    }

    /**
     * @depends  testAddUserEntry
     */
    public function testRemoveAddLocationEntry($user_location) {
        $location = $user_location->locations[0];
        $this->assertInstanceOf('\Goteo\Model\Location', $location);

        $this->assertTrue($user_location->delete());
        $user_location2 = UserLocation::get($user_location->user);

        $this->assertFalse($user_location2);
        $this->assertTrue($location->delete());

        $location2 = Location::get($location->id);
        $this->assertFalse($location2);

    }
}
