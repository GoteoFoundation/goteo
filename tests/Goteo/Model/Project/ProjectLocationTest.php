<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\User;
use Goteo\Model\User\UserLocation;

class ProjectLocationTest extends \PHPUnit_Framework_TestCase {
    private static $data = array(
            'city' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'country_code' => 'XX',
            'latitude' => 0.1234567890,
            'longitude' => -0.1234567890,
            'method' => 'ip',
            'id' => '012-simulated-project-test-210'
        );
    private static $project = array('id' => '012-simulated-project-test-210',
                                    'owner' => '012-simulated-user-test-210',
                                    'name' => '012 Simulated Project Test 210');
    private static $user = array(
            'userid' => '012-simulated-user-test-210',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test@goteo.org'
        );
    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $location = new ProjectLocation();

        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $location);

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

    public function testAddProjectLocation() {
        $project_location = new ProjectLocation(self::$data);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location);
        $this->assertEquals($project_location->latitude, self::$data['latitude']);
        $this->assertEquals($project_location->longitude, self::$data['longitude']);
        $this->assertEquals($project_location->city, self::$data['city']);
        $this->assertEquals($project_location->region, self::$data['region']);
        $this->assertEquals($project_location->country, self::$data['country']);
        $this->assertEquals($project_location->country_code, self::$data['country_code']);
        $this->assertEquals($project_location->user, self::$data['user']);


        return $project_location;
    }
    /**
     * @depends testAddProjectLocation
     */
    public function testSaveProjectLocationNonProject($project_location) {
        // We don't care if exists or not the test user:
        if($user = User::get(self::$user['userid'])) {
            $user->delete();
        }
        //delete test project if exists
        try {
            $project = Project::get(self::$data['id']);
            $project->delete();
        } catch(\Exception $e) {
            // project not exists, ok
        }
        try {
            $project = Project::get(self::$data['id']);
        } catch(\Exception $e) {
            // project not exists, ok
            $this->assertInstanceOf('\Goteo\Core\Error', $e);
        }

        $this->assertFalse($project_location->save());
    }

    public function testCreateProject() {

        $user = new User(self::$user);
        $this->assertTrue($user->save($errors, array('password')));
        $this->assertInstanceOf('\Goteo\Model\User', $user);

        $project = new Project(self::$project);
        $errors = array();
        $this->assertTrue($project->validate($errors), print_r($errors, 1));
        $this->assertNotFalse($project->create(GOTEO_NODE, $errors), print_r($errors, 1));
        $project->name = self::$project['name'];
        $this->assertTrue($project->save($errors), print_r($errors, 1));
        $this->assertTrue($project->rebase(null, $errors), print_r($errors, 1));


        $project = Project::get(self::$data['id']);
        $this->assertEquals($project->id, self::$data['id']);
        // print_r($project);
    }

    /**
     * @depends testAddProjectLocation
     */
    public function testSaveProjectLocation($project_location) {
        $errors = array();
        $this->assertTrue($project_location->validate($errors), print_r($errors, 1));
        $this->assertTrue($project_location->save($errors), print_r($errors, 1));

        $project_location2 = ProjectLocation::get(self::$data['id']);

        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location2);
        $this->assertEquals($project_location->id, $project_location2->id);
        $this->assertEquals($project_location->longitude, $project_location2->longitude);
        $this->assertEquals($project_location->latitude, $project_location2->latitude);
        $this->assertEquals($project_location->method, $project_location2->method);

        $this->assertEquals($project_location2->latitude, self::$data['latitude']);
        $this->assertEquals($project_location2->longitude, self::$data['longitude']);
        $this->assertEquals($project_location2->city, self::$data['city']);
        $this->assertEquals($project_location2->region, self::$data['region']);
        $this->assertEquals($project_location2->country, self::$data['country']);
        $this->assertEquals($project_location2->country_code, self::$data['country_code']);
        $this->assertEquals($project_location2->id, self::$data['id']);

        return $project_location2;
    }

    /**
     * @depends  testSaveProjectLocation
     */
    public function testSetLocable($project_location) {
        $errors = array();
        $this->assertTrue($project_location::setLocable($project_location->id, $errors), print_r($errors, 1));
        $project_location2 = ProjectLocation::get($project_location->id);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location2);
        $this->assertEquals($project_location->id, $project_location2->id);
        $this->assertEquals($project_location->longitude, $project_location2->longitude);
        $this->assertEquals($project_location->latitude, $project_location2->latitude);
        $this->assertEquals($project_location->method, $project_location2->method);

        $this->assertEquals($project_location2->latitude, self::$data['latitude']);
        $this->assertEquals($project_location2->longitude, self::$data['longitude']);
        $this->assertEquals($project_location2->city, self::$data['city']);
        $this->assertEquals($project_location2->region, self::$data['region']);
        $this->assertEquals($project_location2->country, self::$data['country']);
        $this->assertEquals($project_location2->country_code, self::$data['country_code']);
        $this->assertEquals($project_location2->id, self::$data['id']);
        $this->assertFalse($project_location::isUnlocable($project_location->id));
        $this->assertTrue($project_location2->locable);
        $this->assertEquals($project_location->locations, $project_location2->locations);

        $project_location::setUnlocable($project_location->id);
        $project_location2 = ProjectLocation::get($project_location2->id);
        $this->assertTrue($project_location::isUnlocable($project_location->id));
        $this->assertFalse($project_location2->locable);
        $project_location::setLocable($project_location->id);
        $this->assertTrue($project_location2->locable);

        return $project_location;
    }

    /**
     * @depends  testSetLocable
     */
    public function testSetProperty($project_location) {
        $errors = array();
        $txt = "Test info for location";
        $this->assertTrue($project_location::setProperty($project_location->id, 'info', $txt, $error), print_r($errors, 1));
        $project_location2 = ProjectLocation::get($project_location->id);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location2);
        $this->assertEquals($project_location2->info, $txt);

        return $project_location;
    }


    /**
     * @depends testSaveProjectLocation
     */
    public function testNearbyEmpty($project_location) {
        $errors = array();
        $project = new Project;
        $loc = new ProjectLocation($project);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $loc);
        $sibilings = $loc->getSibilingsNearby();
        $this->assertInternalType('array', $sibilings);
        $this->assertEmpty($sibilings);

        return $project_location;
    }

    /**
     * @depends testNearbyEmpty
     */
    public function testNearbyEmptyProjects($project_location) {

        $projects_nearby = ProjectLocation::getNearby(new UserLocation, 1);
        $this->assertInternalType('array', $projects_nearby);
        $this->assertEmpty($projects_nearby);
    }

    /**
     * @depends testNearbyEmpty
     */
    public function testNearby($project_location) {
        // create location for user
        $data = self::$data;
        $data['id'] = self::$user['userid'];
        $data['latitude'] = $data['latitude'] + 0.0001;
        $data['longitude'] = $data['longitude'] + 0.0001;
        $user_location = new UserLocation($data);
        $errors = array();
        $this->assertTrue($user_location->save($errors), print_r($errors, 1));

        $projects_nearby = ProjectLocation::getNearby($user_location, 100);
        // print_r($projects_nearby);die;
        $this->assertInternalType('array', $projects_nearby);
        $keys = array();
        foreach($projects_nearby as $ob) {
            $this->assertInstanceOf('\Goteo\Model\Project\Projectlocation', $ob);
            $keys[] = $ob->id;
        }
        $this->assertContains($project_location->id, $keys);

    }

    /**
     * @depends  testSetProperty
     */
    public function testRemoveAddLocationEntry($project_location) {

        $this->assertTrue($project_location->delete());
        $project_location2 = ProjectLocation::get($project_location->id);

        $this->assertFalse($project_location2);

    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        //delete test project if exists
        try {
            $project = Project::get(self::$data['id']);
            $project->delete();
        } catch(\Exception $e) {
            // project not exists, ok
        }
        if($user = User::get(self::$user['userid'])) {
            $user->delete();
        }
    }
}
