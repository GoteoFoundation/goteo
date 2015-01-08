<?php


namespace Goteo\Model\Tests;

use Goteo\Model\OpenTag;

class OpenTagTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('name' => 'test name', 'description' => 'test description', 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new OpenTag();

        $this->assertInstanceOf('\Goteo\Model\OpenTag', $ob);

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
        $ob = new OpenTag(self::$data);

        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());
        $ob = OpenTag::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\OpenTag', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(OpenTag::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = OpenTag::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(OpenTag::delete($ob->id));
    }
}
