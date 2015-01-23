<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Post;

class PostTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('title' => 'Test post', 'text' => 'test text',
        'blog' => 1,
        // 'date' => '2015-01-01',
        'publish' => 1
        );

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Post();

        $this->assertInstanceOf('\Goteo\Model\Post', $ob);

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
        $ob = new Post(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Post::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Post', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key : $val] != [{$ob->key}]");
        }

        $this->assertTrue($ob->delete());

        $ob = new Post(self::$data);
        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Post::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Post::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Post::delete($ob->id));
    }
}
