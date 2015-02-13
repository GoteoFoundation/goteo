<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Category;

class CategoryTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('name' => 'Test category', 'description' => 'Test description');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Category();

        $this->assertInstanceOf('\Goteo\Model\Category', $ob);

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
        $ob = new Category(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $ob = Category::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Category', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Category::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Category::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Category::delete($ob->id));
    }
}
