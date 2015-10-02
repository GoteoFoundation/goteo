<?php


namespace Goteo\Model\Blog\Post\Tests;

use Goteo\Model\Blog\Post\Tag;

class TagTest extends \PHPUnit_Framework_TestCase {
    private static $data = array('name' => 'Test category');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Tag();

        $this->assertInstanceOf('\Goteo\Model\Blog\Post\Tag', $ob);

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
        $ob = new Tag(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Tag::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Blog\Post\Tag', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Tag::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Tag::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Tag::delete($ob->id));
    }
}
