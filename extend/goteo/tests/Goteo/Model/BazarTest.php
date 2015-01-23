<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Bazar;

class BazarTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('reward' => 1, 'project' => 'test', 'title' => 'test title', 'description' => 'test description', 'order' => 0, 'active' => 0);

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

    public function testCreate() {
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
}
