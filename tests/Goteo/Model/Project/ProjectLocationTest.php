<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Core\DB;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\User;
use Goteo\Model\User\UserLocation;

class ProjectLocationTest extends \PHPUnit\Framework\TestCase {
    private static $data = array(
            'city' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'country_code' => 'XX',
            'latitude' => 0.1234567890,
            'longitude' => -0.1234567890,
            'method' => 'ip',
        );

    public function testInstance(): ProjectLocation
    {
        DB::cache(false);

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

    public function testAddProjectLocation(): ProjectLocation
    {
        self::$data['id'] = 'test-project-non-existing';
        $project_location = new ProjectLocation(self::$data);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location);
        $this->assertEquals($project_location->latitude, self::$data['latitude']);
        $this->assertEquals($project_location->longitude, self::$data['longitude']);
        $this->assertEquals($project_location->city, self::$data['city']);
        $this->assertEquals($project_location->region, self::$data['region']);
        $this->assertEquals($project_location->country, self::$data['country']);
        $this->assertEquals($project_location->country_code, self::$data['country_code']);
        $this->assertEquals($project_location->id, self::$data['id']);

        return $project_location;
    }

    /**
     * @depends testAddProjectLocation
     */
    public function testSaveProjectLocationNonProject(ProjectLocation $project_location): ProjectLocation
    {
        delete_test_project();
        delete_test_user();

        $this->assertFalse($project_location->save());
        return $project_location;
    }

    /**
     * @depends testSaveProjectLocationNonProject
     */
    public function testCreateProject(ProjectLocation $project_location): ProjectLocation
    {
        $user = get_test_user();
        $this->assertInstanceOf('\Goteo\Model\User', $user);

        $project = get_test_project();
        $this->assertInstanceOf('\Goteo\Model\Project', $project);

        self::$data['id'] = $project->id;
        $project_location->id = $project->id;
        return $project_location;
    }

    /**
     * @depends testCreateProject
     */
    public function testSaveProjectLocation(ProjectLocation $project_location) {
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
    public function testSetLocable(ProjectLocation $project_location) {
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
        $project_location2::setLocable($project_location2->id);
        $this->assertFalse($project_location::isUnlocable($project_location->id));

        return $project_location;
    }

    /**
     * @depends  testSetLocable
     */
    public function testSetProperty(ProjectLocation $project_location): ProjectLocation
    {
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
    public function testNearbyEmpty(ProjectLocation $project_location) {
        $project = new Project;
        $loc = new ProjectLocation($project);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $loc);
        $siblings = $loc->getSibilingsNearby();
        $this->assertIsArray($siblings);
        $this->assertEmpty($siblings);

        return $project_location;
    }

    /**
     * @depends testNearbyEmpty
     */
    public function testNearbyEmptyProjects() {
        $projects_nearby = ProjectLocation::getNearby(new UserLocation, 1);
        $this->assertIsArray($projects_nearby);
        $this->assertEmpty($projects_nearby);
    }

    /**
     * @depends testNearbyEmpty
     */
    public function testNearby(ProjectLocation $project_location) {
        // create location for user
        $data = self::$data;
        $user = get_test_user();
        $data['id'] = $user->id;
        $data['latitude'] = $data['latitude'] + 0.0001;
        $data['longitude'] = $data['longitude'] + 0.0001;
        $user_location = new UserLocation($data);
        $errors = array();
        $this->assertTrue($user_location->save($errors), print_r($errors, 1));

        $projects_nearby = ProjectLocation::getNearby($user_location, 100);
        $this->assertIsArray($projects_nearby);
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
    public function testRemoveAddLocationEntry(ProjectLocation $project_location) {
        $this->assertTrue($project_location->dbDelete());
        $project_location2 = ProjectLocation::get($project_location->id);

        $this->assertFalse($project_location2);
    }

    static function tearDownAfterClass(): void {
        delete_test_project();
        delete_test_user();
    }
}
