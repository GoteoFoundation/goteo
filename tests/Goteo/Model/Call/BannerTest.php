<?php


namespace Goteo\Model\Call\Tests;

use Goteo\Model\Call\Banner;

class BannerTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('name' => 'test name', 'call' => 'test', 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Banner();

        $this->assertInstanceOf('\Goteo\Model\Call\Banner', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate(), print_r($errors, 1));
        $this->assertFalse($ob->save());
        return $ob;
    }
/*
    public function testCreate() {
        $ob = new Banner(self::$data);

        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());
        $ob = Banner::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Call\Banner', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
        }

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Banner::delete($ob->id));

        return $ob;
    }
    */
    /**
     * @depends testValidate
     */
    public function testNonExisting($ob) {
        $ob = Banner::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Banner::delete($ob->id));
    }
}
