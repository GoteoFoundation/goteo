<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Promote;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class PromoteTest extends TestCase {

    private static $data = array('title' => 'test title', 'description' => 'test description', 'order' => 0, 'active' => 0);
    private static $trans_data = array('title' => 'Test títol', 'description' => 'test descripció');

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
        delete_test_project();
        delete_test_user();
        delete_test_node();
        self::$data['node'] = get_test_node()->id;
        $ob = new Promote(self::$data);
        $this->assertFalse($ob->save());
    }

    public function testCreateProject() {
        $project = get_test_project();
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
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testSaveLanguages($ob) {
        $errors = [];
        $this->assertTrue($ob->setLang('ca', self::$trans_data, $errors), print_r($errors, 1));
        return $ob;
    }

    /**
     * @depends testSaveLanguages
     */
    public function testCheckLanguages($ob) {
        $new = Promote::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\Promote', $new);
        $this->assertEquals(self::$data['title'], $new->title);
        $this->assertEquals(self::$data['description'], $new->description);
        Lang::set('ca');
        $new2 = Promote::get($ob->id);
        $this->assertEquals(self::$trans_data['title'], $new2->title);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');

    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $this->assertTrue(Promote::delete($ob->id));
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
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }
}
