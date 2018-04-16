<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Post;

class PostTest extends \PHPUnit_Framework_TestCase {
    private static $related_tables = array('post_node' => 'post',
                    'post_image' => 'post',
                    'post_lang' => 'id',
                    'post_tag' => 'post');
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
    public function testValidatePost($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    /**
     * @depends testInstance
     */
    public function testCreatePost() {
        $ob = new Post(self::$data);
        $errors = [];
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $ob = Post::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Post', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key : $val] != [{$ob->key}]");
        }

        $this->assertTrue($ob->dbDelete());


        $ob = new Post(self::$data);
        $this->assertTrue($ob->save());

        return $ob;

    }

    /**
     * @depends testCreatePost
     */
    public function testGetPost($ob) {
        $this->assertInstanceOf('\Goteo\Model\Post', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key : $val] != [{$ob->key}]");
        }

        return $ob;
    }
    /**
     * @depends testGetPost
     */
    public function testDeletePost($ob) {
        //delete statically
        $this->assertTrue(Post::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testDeletePost
     */
    public function testNonExisting($ob) {
        $ob = Post::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Post::delete($ob->id));
    }

    public function testCleanProjectRelated() {
        foreach(self::$related_tables as $tb => $field) {
            $this->assertEquals(0, Post::query("SELECT COUNT(*) FROM $tb WHERE $field NOT IN (SELECT id FROM post)")->fetchColumn(), "DB incoherences in table [$tb], Please run SQL command:\nDELETE FROM $tb WHERE $field NOT IN (SELECT id FROM post)");
        }
    }

}
