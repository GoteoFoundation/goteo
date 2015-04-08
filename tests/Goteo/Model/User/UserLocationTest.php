<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\UserLocation;

class UserLocationTest extends \PHPUnit_Framework_TestCase {
    private static $data = array(
            'city' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'country_code' => 'XX',
            'latitude' => '0.1234567890',
            'longitude' => '-0.1234567890',
            'method' => 'ip',
            'id' => '012-simulated-user-test-210'
        );
    private static $user = array(
            'userid' => '012-simulated-user-test-210',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test@goteo.org'
        );
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
        $this->assertFalse($location->validate($errors));
        $this->assertFalse($location->save());
    }

    public function testAddUserLocation() {

        $user_location = new UserLocation(self::$data);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location);
        $this->assertEquals($user_location->latitude, self::$data['latitude']);
        $this->assertEquals($user_location->longitude, self::$data['longitude']);
        $this->assertEquals($user_location->city, self::$data['city']);
        $this->assertEquals($user_location->region, self::$data['region']);
        $this->assertEquals($user_location->country, self::$data['country']);
        $this->assertEquals($user_location->country_code, self::$data['country_code']);
        $this->assertEquals($user_location->id, self::$data['id']);


        return $user_location;
    }

    /**
     * @depends testAddUserLocation
     */
    public function testSaveUserLocationNonUser($user_location) {
        // We don't care if exists or not the test user:
        if($user = \Goteo\Model\User::get(self::$user['userid'])) {
            $user->delete();
        }

        $this->assertFalse($user_location->save());
    }

    public function testCreateUser() {
        $user = new \Goteo\Model\User(self::$user);
        $this->assertTrue($user->save($errors, array('password')));
        $this->assertInstanceOf('\Goteo\Model\User', $user);
    }

    /**
     * @depends testAddUserLocation
     */
    public function testSaveUserLocation($user_location) {
        $errors = array();
        $this->assertTrue($user_location->validate($errors), print_r($errors, 1));
        $this->assertTrue($user_location->save($errors), print_r($errors, 1));

        $user_location2 = UserLocation::get(self::$data['id']);

        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location2);
        $this->assertEquals($user_location->id, $user_location2->id);
        $this->assertEquals($user_location->longitude, $user_location2->longitude);
        $this->assertEquals($user_location->latitude, $user_location2->latitude);
        $this->assertEquals($user_location->method, $user_location2->method);

        $this->assertEquals($user_location2->latitude, self::$data['latitude']);
        $this->assertEquals($user_location2->longitude, self::$data['longitude']);
        $this->assertEquals($user_location2->city, self::$data['city']);
        $this->assertEquals($user_location2->region, self::$data['region']);
        $this->assertEquals($user_location2->country, self::$data['country']);
        $this->assertEquals($user_location2->country_code, self::$data['country_code']);
        $this->assertEquals($user_location2->id, self::$data['id']);

        return $user_location2;
    }

    /**
     * @depends  testSaveUserLocation
     */
    public function testSetLocable($user_location) {
        $errors = array();
        $this->assertTrue($user_location::setLocable($user_location->id, $errors), print_r($errors, 1));
        $user_location2 = UserLocation::get($user_location->id);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location2);
        $this->assertEquals($user_location->id, $user_location2->id);
        $this->assertEquals($user_location->longitude, $user_location2->longitude);
        $this->assertEquals($user_location->latitude, $user_location2->latitude);
        $this->assertEquals($user_location->method, $user_location2->method);

        $this->assertEquals($user_location2->latitude, self::$data['latitude']);
        $this->assertEquals($user_location2->longitude, self::$data['longitude']);
        $this->assertEquals($user_location2->city, self::$data['city']);
        $this->assertEquals($user_location2->region, self::$data['region']);
        $this->assertEquals($user_location2->country, self::$data['country']);
        $this->assertEquals($user_location2->country_code, self::$data['country_code']);
        $this->assertEquals($user_location2->id, self::$data['id']);
        $this->assertFalse($user_location::isUnlocable($user_location->id));
        $this->assertTrue($user_location2->locable);
        $this->assertEquals($user_location->locations, $user_location2->locations);

        $user_location::setUnlocable($user_location->id);
        $user_location2 = UserLocation::get($user_location2->id);
        $this->assertTrue($user_location::isUnlocable($user_location->id));
        $this->assertFalse($user_location2->locable);

        return $user_location;
    }

    /**
     * @depends  testSetLocable
     */
    public function testSetProperty($user_location) {
        $errors = array();
        $txt = "Test info for location";
        $this->assertTrue($user_location::setProperty($user_location->id, 'info', $txt, $error), print_r($errors, 1));
        $user_location2 = UserLocation::get($user_location->id);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $user_location2);
        $this->assertEquals($user_location2->info, $txt);

        return $user_location;
    }
    /**
     * @depends  testSetProperty
     */
    public function testRemoveAddLocationEntry($user_location) {

        $this->assertTrue($user_location->delete());
        $user_location2 = UserLocation::get($user_location->id);

        $this->assertFalse($user_location2);

    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        if($user = \Goteo\Model\User::get(self::$user['userid'])) {
            $user->delete();
        }
    }
}
