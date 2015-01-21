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
            'city' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'country_code' => 'XX',
            'latitude' => '0.1234567890',
            'longitude' => '-0.1234567890',
            'valid' => 1,
            'method' => 'ip',
            'user' => '_simulated_user_test_'
        );
        $user_location = UserLocation::addUserLocation($data);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location);
        foreach($user_location->locations as $loc) {
            $this->assertInstanceOf('\Goteo\Model\Location', $loc);
        }

        $location2 = Location::get($user_location->location);
        $this->assertInstanceOf('\Goteo\Model\Location', $location2);
        $this->assertEquals($user_location->locations[0]->latitude, $location2->latitude);
        $this->assertEquals($user_location->locations[0]->longitude, $location2->longitude);
        $this->assertEquals($user_location->locations[0]->city, $location2->city);
        $this->assertEquals($user_location->locations[0]->region, $location2->region);
        $this->assertEquals($user_location->locations[0]->country, $location2->country);
        $this->assertEquals($user_location->locations[0]->country_code, $location2->country_code);
        $this->assertEquals($user_location->locations[0]->id, $location2->id);
        return $location2;
    }

    /**
     * @depends testAddUserLocation
     */
    public function testAddUserEntry($location) {
        $data = array(
            'location' => $location->id,
            'method' => 'ip',
            'user' => '_simulated_user_test_'
        );
        $user_location = new UserLocation($data);
        $this->assertTrue($user_location->validate($errors), print_r($errors, 1));
        $this->assertTrue($user_location->save());
        $user_location2 = UserLocation::get($user_location->user);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location2);
        $this->assertEquals($user_location->location, $user_location2->location);
        $this->assertEquals($user_location->method, $user_location2->method);
        $this->assertInternalType('array', $user_location2->locations);
        foreach($user_location2->locations as $loc) {
            $this->assertInstanceOf('\Goteo\Model\Location', $loc);
        }

        return $user_location2;
    }

    /**
     * @depends  testAddUserEntry
     */
    public function testSetLocable($user_location) {

        $this->assertTrue($user_location::setLocable($user_location->user, $error), print_r($errors, 1));
        $user_location2 = UserLocation::get($user_location->user);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location2);
        $this->assertFalse($user_location::isUnlocable($user_location->user));
        $this->assertTrue($user_location2->locable);
        $this->assertEquals($user_location->locations, $user_location2->locations);

        $user_location::setUnlocable($user_location->user);
        $user_location2 = UserLocation::get($user_location2->user);
        $this->assertTrue($user_location::isUnlocable($user_location->user));
        $this->assertFalse($user_location2->locable);

        return $user_location;
    }
    /**
     * @depends  testSetLocable
     */
    public function testSetProperty($user_location) {
        $txt = "Test info for location";
        $this->assertTrue($user_location::setProperty($user_location->user, 'info', $txt, $error), print_r($errors, 1));
        $user_location2 = UserLocation::get($user_location->user);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location2);
        $this->assertEquals($user_location2->info, $txt);

        return $user_location;
    }
    /**
     * @depends  testSetProperty
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
