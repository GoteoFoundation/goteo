<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Promote;
use Goteo\Model\User;
use Goteo\Model\Project;

class PromoteTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('project' => '012-simulated-project-test-210', 'node' => 'goteo', 'title' => 'test title', 'description' => 'test description', 'order' => 0, 'active' => 0);
    private static $project = array('id' => '012-simulated-project-test-210',
                                    'owner' => '012-simulated-user-test-210',
                                    'node' => 'goteo',
                                    'name' => '012 Simulated Project Test 210');
    private static $user = array(
            'userid' => '012-simulated-user-test-210',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test@goteo.org'
        );
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
        $ob = new Promote(self::$data);
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

        $this->assertFalse($ob->save());
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
        $this->assertTrue($project->rebase(self::$data['project'], $errors), print_r($errors, 1));


        $project = Project::get(self::$data['project']);
        $this->assertEquals($project->id, self::$data['project']);
        // print_r($project);
    }

    public function testCreate() {
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
        //delete test project if exists
        try {
            $project = Project::get(self::$data['project']);
            $project->delete();
        } catch(\Exception $e) {
            // project not exists, ok
        }
        if($user = User::get(self::$user['userid'])) {
            $user->delete();
        }
    }
}
