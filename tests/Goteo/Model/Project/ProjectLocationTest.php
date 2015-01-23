<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Location;

class LocationTest extends \PHPUnit_Framework_TestCase {

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
        $data = array(
            'city' => 'Simulated City',
            'region' => 'Simulated Region',
            'country' => 'Neverland',
            'country_code' => 'XX',
            'latitude' => '0.1234567890',
            'longitude' => '-0.1234567890',
            'valid' => 1,
            'method' => 'ip',
            'project' => '_simulated_project_test_'
        );
        $project_location = ProjectLocation::addProjectLocation($data);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location);
        foreach($project_location->locations as $loc) {
            $this->assertInstanceOf('\Goteo\Model\Location', $loc);
        }

        $location2 = Location::get($project_location->location);
        $this->assertInstanceOf('\Goteo\Model\Location', $location2);
        $this->assertEquals($project_location->locations[0]->latitude, $location2->latitude);
        $this->assertEquals($project_location->locations[0]->longitude, $location2->longitude);
        $this->assertEquals($project_location->locations[0]->city, $location2->city);
        $this->assertEquals($project_location->locations[0]->region, $location2->region);
        $this->assertEquals($project_location->locations[0]->country, $location2->country);
        $this->assertEquals($project_location->locations[0]->country_code, $location2->country_code);
        $this->assertEquals($project_location->locations[0]->id, $location2->id);
        return $location2;
    }

    /**
     * @depends testAddProjectLocation
     */
    public function testAddProjectEntry($location) {
        $data = array(
            'location' => $location->id,
            'method' => 'ip',
            'project' => '_simulated_project_test_'
        );
        $project_location = new ProjectLocation($data);
        $this->assertTrue($project_location->validate($errors), print_r($errors, 1));
        $this->assertTrue($project_location->save());
        $project_location2 = ProjectLocation::get($project_location->project);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location2);
        $this->assertEquals($project_location->location, $project_location2->location);
        $this->assertEquals($project_location->method, $project_location2->method);
        $this->assertInternalType('array', $project_location2->locations);
        foreach($project_location2->locations as $loc) {
            $this->assertInstanceOf('\Goteo\Model\Location', $loc);
        }

        return $project_location2;
    }

    /**
     * @depends  testAddProjectEntry
     */
    public function testSetLocable($project_location) {

        $this->assertTrue($project_location::setLocable($project_location->project, $error), print_r($errors, 1));
        $project_location2 = ProjectLocation::get($project_location->project);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location2);
        $this->assertFalse($project_location::isUnlocable($project_location->project));
        $this->assertTrue($project_location2->locable);
        $this->assertEquals($project_location->locations, $project_location2->locations);

        $project_location::setUnlocable($project_location->project);
        $project_location2 = ProjectLocation::get($project_location2->project);
        $this->assertTrue($project_location::isUnlocable($project_location->project));
        $this->assertFalse($project_location2->locable);

        return $project_location;
    }
    /**
     * @depends  testSetLocable
     */
    public function testSetProperty($project_location) {
        $txt = "Test info for location";
        $this->assertTrue($project_location::setProperty($project_location->project, 'info', $txt, $error), print_r($errors, 1));
        $project_location2 = ProjectLocation::get($project_location->project);
        $this->assertInstanceOf('\Goteo\Model\Project\ProjectLocation', $project_location2);
        $this->assertEquals($project_location2->info, $txt);

        return $project_location;
    }
    /**
     * @depends  testSetProperty
     */
    public function testRemoveAddLocationEntry($project_location) {
        $location = $project_location->locations[0];
        $this->assertInstanceOf('\Goteo\Model\Location', $location);

        $this->assertTrue($project_location->delete());
        $project_location2 = ProjectLocation::get($project_location->project);

        $this->assertFalse($project_location2);
        $this->assertTrue($location->delete());

        $location2 = Location::get($location->id);
        $this->assertFalse($location2);

    }
}
