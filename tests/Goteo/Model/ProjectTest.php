<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Project;
use Goteo\Model\User;

class ProjectTest extends \PHPUnit_Framework_TestCase {
    private static $related_tables = array('project_category' => 'project',
                    'project_account' => 'project',
                    'project_conf' => 'project',
                    'project_data' => 'project',
                    'project_image' => 'project',
                    'project_lang' => 'id',
                    'project_location' => 'id',
                    'project_open_tag' => 'project');

    private static $data = array('id' => '012-simulated-project-test-210', 'owner' => '012-simulated-user-test-210', 'name' => '012 Simulated Project Test 210');
    private static $user = array(
            'userid' => '012-simulated-user-test-210',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test@goteo.org'
        );

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Project();

        $this->assertInstanceOf('\Goteo\Model\Project', $ob);

        return $ob;
    }

    public function testCount() {

        $total = Project::countTotal();
        $campaign = Project::countTotal(array('status' => Project::STATUS_IN_CAMPAIGN));
        $funded = Project::countTotal(array('status' => Project::STATUS_FUNDED));
        $unfunded = Project::countTotal(array('status' => Project::STATUS_UNFUNDED));
        $mainnode = Project::countTotal(array('node' => 'goteo'));

        $this->assertInternalType('integer', $total);
        $this->assertInternalType('integer', $campaign);
        $this->assertInternalType('integer', $funded);
        $this->assertInternalType('integer', $unfunded);
        $this->assertInternalType('integer', $mainnode);
        $this->assertGreaterThanOrEqual($campaign, $total);
        $this->assertGreaterThanOrEqual($funded, $total);
        $this->assertGreaterThanOrEqual($unfunded, $total);
        $this->assertGreaterThanOrEqual($mainnode, $total);
        echo "Projects: [$total] In Campaign: [$campaign] Funded: [$funded]  Unfunded: [$unfunded] Node goteo: [$mainnode]\n";
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
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
    public function testCreateProject($user) {
        $this->assertEquals($user->id, self::$data['owner']);

        $errors = array();
        $project = new Project(self::$data);
        $this->assertTrue($project->validate($errors), print_r($errors, 1));
        $this->assertNotFalse($project->create(GOTEO_NODE, $errors), print_r($errors, 1));

        $project = Project::get($project->id);
        $this->assertInstanceOf('\Goteo\Model\Project', $project);

        $this->assertEquals($project->owner, self::$user['userid']);
        return $project;
    }


    /**
     * @depends testCreateProject
     */
    public function testEditProject($project) {
        $errors = array();
        $project->name = self::$data['name'];
        $this->assertTrue($project->save($errors), print_r($errors, 1));

        $project = Project::get($project->id);

        $this->assertEquals($project->owner, self::$user['userid']);
        $this->assertEquals($project->name, self::$data['name']);

    }

    /**
     * @depends testCreateProject
     */
    public function testRebaseProject($project) {
        $errors = array();

        $this->assertRegExp('/^[A-Fa-f0-9]{32}$/', $project->id, $project->id);
        $this->assertTrue($project->rebase(null, $errors), print_r($errors, 1));
        $this->assertNotRegExp('/^[A-Fa-f0-9]{32}$/', $project->id, $project->id);
        $this->assertEquals($project->id, self::$data['id']);

        $project = Project::get(self::$data['id']);
        $this->assertEquals($project->id, self::$data['id']);

        $newid = self::$data['id'] . '-2';

        $this->assertTrue($project->rebase($newid, $errors), print_r($errors, 1));
        $this->assertEquals($project->id, $newid);
        $project = Project::get($newid);
        $this->assertEquals($project->id, $newid);

        $newid = self::$data['id'];

        $this->assertTrue($project->rebase($newid, $errors), print_r($errors, 1));
        $this->assertEquals($project->id, $newid);
        $project = Project::get($newid);
        $this->assertEquals($project->id, $newid);

        return $project;
    }

    /**
     * @depends testCreateProject
     */
    public function testDeleteProject($project) {
        $errors = array();
        $this->assertTrue($project->delete($errors), print_r($errors, 1));

        return $project;
    }

    public function testNonExisting() {
        try {
            $ob = Project::get(self::$data['id']);
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Core\Error', $e);
        }
        try {
            $ob = Project::get('non-existing-project');
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Core\Error', $e);
        }
    }

    public function testCleanProjectRelated() {
        foreach(self::$related_tables as $tb => $field) {
            $this->assertEquals(0, Project::query("SELECT COUNT(*) FROM $tb WHERE $field NOT IN (SELECT id FROM project)")->fetchColumn(), "DB incoherences in table [$b], Please run SQL command:\nDELETE FROM $tb WHERE $field NOT IN (SELECT id FROM project)");
        }
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
