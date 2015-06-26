<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Banner;

class BannerTest extends \PHPUnit_Framework_TestCase {
    protected static $test_img ;
    protected static $data ;
    //read config
    public static function setUpBeforeClass() {

       //temp file
        self::$test_img = __DIR__ . '/test.png';
        self::$data = array('project' => 'test', 'image' => self::$test_img, 'node' => 0, 'order' => 0, 'active' => 0);

        file_put_contents(self::$test_img, base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABkElEQVRYhe3Wv2qDQBgA8LxJH8BXcHLN4pCgBxIOddAlSILorFDaQRzFEHEXUWyXlo6BrkmeI32Hr1PTMyb1rtpIIQff6vdTvz83unt+giFjdAP8awCXZ8Dl2XCAcRjAOAyGA8iaDrKmDwMQ4ggQUgAhBYQ4uj5AMswjQDLM6wJE3zsm/wrR964D4NOkkbzLr2AC8GkC8gxfBMgzDHya/A2AyzOQNf1i8iNC05lmAxWAy7Na0bWFZJjUCCrAdLmoJbDmFlRFCe+bDVhz6yxiulz0AyD7HSEFHu8fgDyu7XQqylbAxP1O4NoOnB6M1YuAiet0B5CF9/by2gC0FWRnAPnAj8OBCYCQ0i+A9vQKIAfPfrtrTb7f7mqDqTOAbMF1vGoFrOMVUyu2AsZhUPukP30F8u0RUqguK1SDiJyCGKtQFWUjeVWUtZakXdFUgHNLCGMVXNsB13Yas4BlKVEvIz5NqJcRy0ZkWsdcnoHoe2dXsjzDIPoe8y3511cyPk1AiCMQ4oj5DtALoK+4AQYHfALaYBdH6m2UnQAAAABJRU5ErkJggg=='));

    }
    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $converter = new Banner();

        $this->assertInstanceOf('\Goteo\Model\Banner', $converter);

        return $converter;
    }

    public function testCreate() {
        $ob = new Banner(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Banner::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Banner', $ob);

        $this->assertEquals($ob->project, self::$data['project']);
        $this->assertInstanceOf('\Goteo\Model\Image', $ob->image);
        $this->assertEquals($ob->image->name, self::$data['image']);

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Banner::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Banner::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Banner::delete($ob->id));
    }

    /**
     * Remove temporal files on finish
     */
    public static function tearDownAfterClass($fp) {
        unlink(self::$test_img);
    }
}
