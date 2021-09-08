<?php


namespace Goteo\Model\Blog\Tests;

use Goteo\Core\DB;
use Goteo\Model\Blog\Post;
use Goteo\Model\Image;

class PostTest extends \PHPUnit\Framework\TestCase {
    private static $data = array('title' => 'Test post', 'text' => 'test text',
        'blog' => 1,
        'date' => '2015-01-01',
        'allow' => 1,
        'publish' => 1);

    private static $related_tables = array('post_node' => 'post',
                    'post_image' => 'post',
                    'post_lang' => 'id',
                    'post_tag' => 'post');

    private static $image = array(
                        'name' => 'test.png',
                        'type' => 'image/png',
                        'tmp_name' => '',
                        'error' => '',
                        'size' => 0);

    private static $image2;

    public static function setUpBeforeClass(): void {

       //temp file
        $i = base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABkElEQVRYhe3Wv2qDQBgA8LxJH8BXcHLN4pCgBxIOddAlSILorFDaQRzFEHEXUWyXlo6BrkmeI32Hr1PTMyb1rtpIIQff6vdTvz83unt+giFjdAP8awCXZ8Dl2XCAcRjAOAyGA8iaDrKmDwMQ4ggQUgAhBYQ4uj5AMswjQDLM6wJE3zsm/wrR964D4NOkkbzLr2AC8GkC8gxfBMgzDHya/A2AyzOQNf1i8iNC05lmAxWAy7Na0bWFZJjUCCrAdLmoJbDmFlRFCe+bDVhz6yxiulz0AyD7HSEFHu8fgDyu7XQqylbAxP1O4NoOnB6M1YuAiet0B5CF9/by2gC0FWRnAPnAj8OBCYCQ0i+A9vQKIAfPfrtrTb7f7mqDqTOAbMF1vGoFrOMVUyu2AsZhUPukP30F8u0RUqguK1SDiJyCGKtQFWUjeVWUtZakXdFUgHNLCGMVXNsB13Yas4BlKVEvIz5NqJcRy0ZkWsdcnoHoe2dXsjzDIPoe8y3511cyPk1AiCMQ4oj5DtALoK+4AQYHfALaYBdH6m2UnQAAAABJRU5ErkJggg==');
        self::$image2 = self::$image;
        self::$image['tmp_name'] = __DIR__ . '/test-tmp.png';
        self::$image2['tmp_name'] = __DIR__ . '/test-tmp2.png';
        self::$image['name'] = 'other.png';
        file_put_contents(self::$image['tmp_name'], $i);
        file_put_contents(self::$image2['tmp_name'], $i);
        self::$image['size'] = strlen($i);
        self::$image2['size'] = strlen($i);
    }

    public function testInstance(): Post
    {
        DB::cache(false);

        $ob = new Post();

        $this->assertInstanceOf('\Goteo\Model\Blog\Post', $ob);

        return $ob;
    }
    /**
     * @depends testInstance
     */
    public function testValidatePost(Post $ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    /**
     * @depends testInstance
     */
    public function testCreatePost(): Post
    {
        $ob = new Post(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());

        return $ob;
    }

    /**
     * @depends testCreatePost
     */
    public function testGetPost(Post $ob) {
        $ob = Post::getById($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Blog\Post', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key : $val] != [{$ob->key}]");
        }

        return $ob;
    }

    /**
     * @depends testGetPost
     */
    public function testSlugPost(Post $ob) {
        $this->assertEquals('test-post', $ob->getSlug());
        $this->assertEquals($ob->id, Post::getBySlug($ob->getSlug())->id);
        $this->assertEquals($ob->id, Post::getById($ob->id)->id);

        $ob2 = new Post(self::$data);
        $errors = [];
        $this->assertTrue($ob2->save($errors), print_r($errors, 1));

        $this->assertStringContainsString('test-post-', $ob2->getSlug());

        Post::delete($ob2->id);

        return $ob;
    }

    /**
     * @depends testGetPost
     */
    public function testEditPost(Post $ob) {
        //add image
        $ob->title = self::$data['title'] . " (edited)";

        $ob->image = self::$image;

        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());

        //add second image
        $ob->image = self::$image2;
        $this->assertTrue($ob->save());

        $sob = Post::getById($ob->id);
        $this->assertEquals($sob->title, $ob->title);
        $this->assertIsArray($ob->gallery);
        $this->assertIsArray($sob->gallery);
        $this->assertCount(2, $sob->gallery);
        $this->assertEquals($sob->gallery[0]->id, $ob->gallery[0]->id);

        return $sob;
    }

    /**
     * @depends testEditPost
     */
    public function testRemoveImagePost(Post $ob) {
        $errors = array();

        $this->assertTrue($ob->image->remove($errors, 'post'), print_r($errors, 1));
        $ob->gallery = Image::getModelGallery('post', $ob->id);
        $ob->image = Image::getModelImage('', $ob->gallery);
        $this->assertIsArray($ob->gallery);
        $this->assertCount(1, $ob->gallery);
        $this->assertEquals($ob->image->id, $ob->gallery[0]->id);

        //remove second image
        $this->assertEquals($ob->image, Image::getModelImage('', $ob->gallery));
        $this->assertTrue($ob->gallery[0]->remove($errors, 'post'), print_r($errors, 1));
        $ob = Post::getById($ob->id);
        $this->assertIsArray($ob->gallery);
        $this->assertCount(0, $ob->gallery);
        $this->assertEmpty($ob->image);

        //add image (to check autodelete)
        $ob->image = self::$image;

        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Post::getById($ob->id);
        $this->assertIsArray($ob->gallery);
        $this->assertCount(1, $ob->gallery);
        $this->assertEquals($ob->image, $ob->gallery[0]);
        return $ob;
    }

    /**
     * @depends testRemoveImagePost
     */
    public function testDeletePost(Post $ob) {
        //delete post
        $this->assertTrue($ob->dbDelete());

        return $ob;
    }

    /**
     * @depends testDeletePost
     */
    public function testNonExisting(Post $ob) {
        $sob = Post::getById($ob->id);
        $this->assertFalse($sob);
    }

    public function testCleanProjectRelated() {
        foreach(self::$related_tables as $tb => $field) {
            $this->assertEquals(0, Post::query("SELECT COUNT(*) FROM $tb WHERE $field NOT IN (SELECT id FROM post)")->fetchColumn(), "DB incoherences in table [$tb], Please run SQL command:\nDELETE FROM $tb WHERE $field NOT IN (SELECT id FROM post)");
        }
    }

    static function tearDownAfterClass(): void {
        unlink(self::$image['tmp_name']);
        unlink(self::$image2['tmp_name']);
    }
}
