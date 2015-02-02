<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Promote;

class PromoteTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('project' => 'test', 'node' => 'test', 'title' => 'test title', 'description' => 'test description', 'order' => 0, 'active' => 0);

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
}
