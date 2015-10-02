<?php

namespace Goteo\Model\Tests;

use Goteo\Model\Info;
use Goteo\Model\Image;

class InfoTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('title' => 'test title', 'text' => 'test description', 'publish' => 0);

    private static $related_tables = array(
                    'info_image' => 'info',
                    'info_lang' => 'id');

    private static $image = array(
                        'name' => 'test.png',
                        'type' => 'image/png',
                        'tmp_name' => '',
                        'error' => '',
                        'size' => 0);
    private static $image2;

    public static function setUpBeforeClass() {

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
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

     /**
     * @depends testInstance
     */
    public function testCreateInfo() {
        $ob = new Info(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());

        return $ob;

    }

    /**
     * @depends testCreateInfo
     */
    public function testGetInfo($ob) {
        $ob = Info::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Info', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key : $val] != [{$ob->key}]");
        }

        return $ob;
    }

    /**
     * @depends testGetInfo
     */
    public function testEditInfo($ob) {
        $ob->title = self::$data['title'] . " (edited)";

        //add image
        $ob->image = self::$image;

        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());

        //add second image
        $ob->image = self::$image2;
        $this->assertTrue($ob->save());

        $sob = Info::get($ob->id);
        $this->assertEquals($sob->title, $ob->title);
        $this->assertInternalType('array', $ob->gallery);
        $this->assertInternalType('array', $sob->gallery);
        $this->assertCount(2, $sob->gallery);
        $this->assertEquals($sob->gallery[0]->id, $ob->gallery[0]->id);
        $this->assertEquals($sob->image->id, $ob->gallery[0]->id);
        return $sob;
    }
    /**
     * @depends testEditInfo
     */
    public function testRemoveImageInfo($ob) {
        $errors = array();
        $this->assertEquals($ob->image, Image::getModelImage('', $ob->gallery));
        $this->assertTrue($ob->image->remove($errors, 'info'), print_r($errors, 1));
        $ob->gallery = Image::getModelGallery('info', $ob->id);
        $ob->image = Image::getModelImage('', $ob->gallery);
        $this->assertInternalType('array', $ob->gallery);
        $this->assertCount(1, $ob->gallery);
        $this->assertEquals($ob->image, $ob->gallery[0]);

        //remove second image
        $this->assertEquals($ob->image, Image::getModelImage('', $ob->gallery));
        $this->assertTrue($ob->gallery[0]->remove($errors, 'info'), print_r($errors, 1));
        $ob = Info::get($ob->id);
        $this->assertInternalType('array', $ob->gallery);
        $this->assertCount(0, $ob->gallery);
        $this->assertEmpty($ob->image);

        //add image (to check autodelete)
        $ob->image = self::$image;

        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Info::get($ob->id);
        $this->assertInternalType('array', $ob->gallery);
        $this->assertCount(1, $ob->gallery);
        $this->assertEquals($ob->image, $ob->gallery[0]);
    }
    /**
     * @depends testGetInfo
     */
    public function testDeleteInfo($ob) {
        //delete post
        $this->assertTrue($ob->dbDelete());

        return $ob;
    }
    public function testCleanProjectRelated() {
        foreach(self::$related_tables as $tb => $field) {
            $this->assertEquals(0, Info::query("SELECT COUNT(*) FROM $tb WHERE $field NOT IN (SELECT id FROM info)")->fetchColumn(), "DB incoherences in table [$tb], Please run SQL command:\nDELETE FROM $tb WHERE $field NOT IN (SELECT id FROM info)");
        }
    }
    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        // Remove temporal files on finish
        unlink(self::$image['tmp_name']);
        unlink(self::$image2['tmp_name']);
    }
}
