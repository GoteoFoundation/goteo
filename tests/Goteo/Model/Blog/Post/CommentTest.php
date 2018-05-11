<?php


namespace Goteo\Model\Blog\Post\Tests;

use Goteo\Model\Blog\Post\Comment;
use Goteo\Model\Blog\Post;

class CommentTest extends \PHPUnit_Framework_TestCase {
    private static $data = array('text' => 'Test comment');
    private static $post_data = array('title' => 'Test post', 'text' => 'test text',
        'blog' => 1,
        'date' => '2015-01-01',
        'allow' => 1,
        'publish' => 1);

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

    public function testCreatePost() {
        $errors = [];
        $ob = new Post(self::$post_data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        return $ob;
    }
    /**
     * @depends testCreatePost
     */
    public function testCreate($post) {
        $errors = [];
        self::$data['post'] = $post->id;
        self::$data['user'] = get_test_user()->id;
        $ob = new Comment(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $ob = Comment::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Blog\Post\Comment', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

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


    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        Post::query('delete from `post` where id = ?', self::$data['post']);
        delete_test_user();
    }
}
