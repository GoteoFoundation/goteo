<?php


namespace Goteo\Model\Blog\Post\Tests;

use Goteo\Model\Blog\Post\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase {
    private static $data = array('text' => 'Test comment', 'post' => 1, 'user' => 'test');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $converter = new Comment();

        $this->assertInstanceOf('\Goteo\Model\Blog\Post\Comment', $converter);

        return $converter;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    public function testCreate() {
        $ob = new Comment(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Comment::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Blog\Post\Comment', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Comment::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Comment::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Comment::delete($ob->id));
    }
}
