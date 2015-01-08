<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Info;

class InfoTest extends \PHPUnit_Framework_TestCase {


    private static $data = array('title' => 'test title', 'text' => 'test description', 'publish' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Info();

        $this->assertInstanceOf('\Goteo\Model\Info', $ob);

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
        $ob = new Info(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());
        $ob = Info::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Info', $ob);

        foreach(self::$data as $key => $val) {
            if($key !== 'project') $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
            // else $this->assertInstanceOf('\Goteo\Model\Project', $ob->$key, "[$key]: " . print_r($ob->$key, 1));
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Info::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Info::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Info::delete($ob->id));
    }
}
