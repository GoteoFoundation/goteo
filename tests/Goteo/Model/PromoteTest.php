<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Promote;
use Goteo\Model\User;
use Goteo\Model\Project;

class PromoteTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('node' => 'goteo', 'title' => 'test title', 'description' => 'test description', 'order' => 0, 'active' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Promote();

        $this->assertInstanceOf('\Goteo\Model\Promote', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
        return $ob;
    }

    /**
     * @depends  testValidate
     */
    public function testSavePromoteNonProject($ob) {
        \delete_tests_project();
        \delete_tests_user();
        $ob = new Promote(self::$data);
        $this->assertFalse($ob->save());
    }

    public function testCreateProject() {
        $project = \get_test_project();
        $this->assertInstanceOf('\Goteo\Model\Project', $project);
        self::$data['project'] = $project->id;
        return $project;
    }

    /**
     * @depends testCreateProject
     */
    public function testCreate($project) {
        $ob = new Promote(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        //TODO: create project
        // $ob = Promote::get($ob->id);
        // $this->assertInstanceOf('\Goteo\Model\Promote', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $this->assertTrue(Promote::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Promote::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Promote::delete($ob->id));
    }
    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        \delete_test_project();
        \delete_test_user();
        \delete_test_node();
    }
}
