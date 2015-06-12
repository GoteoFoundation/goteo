<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Bazar;
use Goteo\Model\Project;

class BazarTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('reward' => 1, 'project' => '012-simulated-project-test-210', 'title' => 'test title', 'description' => 'test description', 'order' => 0, 'active' => 0);
    private static $project_data = array('id' => '012-simulated-project-test-210', 'owner' => '012-simulated-user-test-210', 'name' => '012 Simulated Project Test 210');
    private static $user = array(
            'userid' => '012-simulated-user-test-210',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test@goteo.org'
        );

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Bazar(array('active' => true));

        $this->assertInstanceOf('\Goteo\Model\Bazar', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate(), print_r($errors, 1));
        $this->assertFalse($ob->save());
    }

    public function testCreateUser() {
        // We don't care if exists or not the test user:
        if($user = \Goteo\Model\User::get(self::$user['userid'])) {
            $user->delete();
        }
        $errors = array();
        $user = new \Goteo\Model\User(self::$user);
        $this->assertTrue($user->save($errors, array('password')), print_r($errors, 1));
        $this->assertInstanceOf('\Goteo\Model\User', $user);

        //delete test project if exists
        try {
            $project = Project::get(self::$data['id']);
            $project->delete();
        } catch(\Exception $e) {
            // project not exists, ok
        }
        return $user;
    }

    /**
     * @depends testCreateUser
     */
    public function testCreate($user) {
        $project = new Project(self::$project_data);
        $this->assertTrue($project->validate($errors), print_r($errors, 1));
        $this->assertNotFalse($project->create(GOTEO_NODE, $errors), print_r($errors, 1));
        $project = Project::get($project->id);
        $this->assertInstanceOf('\Goteo\Model\Project', $project);
        self::$data['project'] = $project->id;
        $ob = new Bazar(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());

        $ob = Bazar::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Bazar', $ob);

        foreach(self::$data as $key => $val) {
            if($key !== 'project') $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
            // else $this->assertInstanceOf('\Goteo\Model\Project', $ob->$key, "[$key]: " . print_r($ob->$key, 1));
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Bazar::delete($ob->id));

        $this->assertTrue($project->delete($errors), print_r($errors, 1));
        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Bazar::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Bazar::delete($ob->id));
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        if($user = \Goteo\Model\User::get(self::$user['userid'])) {
            $user->delete();
        }
        // Remove temporal files on finish
    }
}
