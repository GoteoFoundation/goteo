<?php

namespace Goteo\Model\User\Tests;

use Goteo\Model\User;
use Goteo\Model\User\UserLocation;

class UserLocationTest extends \PHPUnit_Framework_TestCase {
    private static $data = array(
            'city' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'country_code' => 'XX',
            'latitude' => 0.1234567890,
            'longitude' => -0.1234567890,
            'method' => 'ip'
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
        delete_test_user();
    }

    public function testAddUserLocation() {
        self::$data['id'] = 'test-user-non-existing';
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
        $this->assertFalse($user_location->save());
        return $user_location;
    }

    /**
     * @depends testSaveUserLocationNonUser
     */
    public function testCreateUser($user_location) {
        $user = get_test_user();
        $this->assertInstanceOf('\Goteo\Model\User', $user);
        self::$data['id'] = $user->id;
        $user_location->id = $user->id;
        return $user_location;
    }

    /**
     * @depends testCreateUser
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
     * @depends testSaveUserLocation
     */
    public function testNearbyEmpty($user_location) {
        $errors = array();
        $user = new User;
        $loc = new UserLocation($user);
        $this->assertInstanceOf('\Goteo\Model\User\UserLocation', $loc);
        $sibilings = $loc->getSibilingsNearby();
        $this->assertInternalType('array', $sibilings);
        $this->assertEmpty($sibilings);
    }

    /**
     * @depends testSaveUserLocation
     */
    public function testAddSecondUser($user_location) {
        $errors = array();
        $user = get_test_user();
        $usr['userid'] = '012-second-test-user-210';
        $usr['name'] = $user->name . ' II';
        $usr['email'] = '2.' . $user->email;
        if(User::get($user->id)) {
            $user->dbDelete();
        }
        $u = new User($usr);
        $this->assertTrue($u->save($errors, array('password')), print_r($errors, 1));
        $this->assertInstanceOf('\Goteo\Model\User', $u);

        $data = self::$data;
        $data['id'] = $usr['userid'];
        $data['latitude'] = $data['latitude'] + 0.0001;
        $data['longitude'] = $data['longitude'] + 0.0001;
        $location2 = new UserLocation($data);
        $errors = array();
        $this->assertTrue($location2->save($errors), print_r($errors, 1));

        return array($user_location, $location2);
    }

    /**
     * @depends testAddSecondUser
     */
    public function testNearby(array $users) {
        $errors = array();
        list($user1, $user2) = $users;
        $sibilings = $user1->getSibilingsNearby(100);
        // print_r($sibilings);
        $this->assertInternalType('array', $sibilings);
        $keys = array();
        foreach($sibilings as $ob) {
            $this->assertInstanceOf('\Goteo\Model\User\Userlocation', $ob);
            $keys[] = $ob->id;
        }
        $this->assertContains($user2->id, $keys);

        $this->assertTrue($user1->dbDelete());
        $this->assertTrue($user2->dbDelete());
    }

    /**
     * @depends  testSetProperty
     */
    public function testRemoveLocationEntry($user_location) {
        $errors = array();
        $this->assertTrue($user_location->dbDelete());
        $user_location2 = UserLocation::get($user_location->id);

        $this->assertFalse($user_location2);

    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        if($user = User::get('012-second-test-user-210')) {
            $user->dbDelete();
        }
        delete_test_user();
    }
}
