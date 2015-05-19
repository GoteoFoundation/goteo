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

    public static function setUpBeforeClass() {

       //temp file
        $i = base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABkElEQVRYhe3Wv2qDQBgA8LxJH8BXcHLN4pCgBxIOddAlSILorFDaQRzFEHEXUWyXlo6BrkmeI32Hr1PTMyb1rtpIIQff6vdTvz83unt+giFjdAP8awCXZ8Dl2XCAcRjAOAyGA8iaDrKmDwMQ4ggQUgAhBYQ4uj5AMswjQDLM6wJE3zsm/wrR964D4NOkkbzLr2AC8GkC8gxfBMgzDHya/A2AyzOQNf1i8iNC05lmAxWAy7Na0bWFZJjUCCrAdLmoJbDmFlRFCe+bDVhz6yxiulz0AyD7HSEFHu8fgDyu7XQqylbAxP1O4NoOnB6M1YuAiet0B5CF9/by2gC0FWRnAPnAj8OBCYCQ0i+A9vQKIAfPfrtrTb7f7mqDqTOAbMF1vGoFrOMVUyu2AsZhUPukP30F8u0RUqguK1SDiJyCGKtQFWUjeVWUtZakXdFUgHNLCGMVXNsB13Yas4BlKVEvIz5NqJcRy0ZkWsdcnoHoe2dXsjzDIPoe8y3511cyPk1AiCMQ4oj5DtALoK+4AQYHfALaYBdH6m2UnQAAAABJRU5ErkJggg==');
        self::$image['tmp_name'] = __DIR__ . '/test-tmp.png';
        file_put_contents(self::$image['tmp_name'], $i);
        self::$image['size'] = strlen($i);
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
        //add image
        $ob->title = self::$data['title'] . " (edited)";

        $ob->image = self::$image;

        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $sob = Info::get($ob->id);
        $this->assertEquals($sob->title, $ob->title);
        $this->assertInternalType('array', $ob->gallery);
        $this->assertInternalType('array', $sob->gallery);
        $this->assertCount(1, $sob->gallery);
        $this->assertEquals($sob->gallery[0]->id, $ob->gallery[0]->id);
        $this->assertEquals($sob->image->id, $ob->gallery[0]->id);
        return $sob;
    }
    /**
     * @depends testGetInfo
     */
    public function testDeleteInfo($ob) {
        //delete post
        $this->assertTrue($ob->delete());

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
    }
}
