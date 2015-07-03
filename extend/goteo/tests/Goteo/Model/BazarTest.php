<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Bazar;
use Goteo\Model\Project;

class BazarTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('reward' => 1, 'title' => 'test title', 'description' => 'test description', 'order' => 0, 'active' => 0);

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
        delete_test_project();
        delete_test_user();

        $this->assertFalse($ob->validate(), print_r($errors, 1));
        $this->assertFalse($ob->save());
    }

    public function testCreate() {
        $user = get_test_user();
        $project = get_test_project();
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

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Bazar::delete($ob->id));

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
        delete_test_project();
        delete_test_user();
    }
}
