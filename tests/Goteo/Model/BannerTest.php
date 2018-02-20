<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Banner;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class BannerTest extends TestCase {
    protected static $test_img ;
    protected static $data ;
    protected static $trans_data ;
    //read config
    public static function setUpBeforeClass() {

        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');

       //temp file
        self::$test_img = __DIR__ . '/test.png';
        self::$data = array('image' => self::$test_img, 'order' => 0, 'active' => 0, 'title' => 'Banner test', 'description' => 'Test description');
        self::$trans_data = array('title' => 'Test Banner', 'description' => 'Test de descripció');

        file_put_contents(self::$test_img, base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABkElEQVRYhe3Wv2qDQBgA8LxJH8BXcHLN4pCgBxIOddAlSILorFDaQRzFEHEXUWyXlo6BrkmeI32Hr1PTMyb1rtpIIQff6vdTvz83unt+giFjdAP8awCXZ8Dl2XCAcRjAOAyGA8iaDrKmDwMQ4ggQUgAhBYQ4uj5AMswjQDLM6wJE3zsm/wrR964D4NOkkbzLr2AC8GkC8gxfBMgzDHya/A2AyzOQNf1i8iNC05lmAxWAy7Na0bWFZJjUCCrAdLmoJbDmFlRFCe+bDVhz6yxiulz0AyD7HSEFHu8fgDyu7XQqylbAxP1O4NoOnB6M1YuAiet0B5CF9/by2gC0FWRnAPnAj8OBCYCQ0i+A9vQKIAfPfrtrTb7f7mqDqTOAbMF1vGoFrOMVUyu2AsZhUPukP30F8u0RUqguK1SDiJyCGKtQFWUjeVWUtZakXdFUgHNLCGMVXNsB13Yas4BlKVEvIz5NqJcRy0ZkWsdcnoHoe2dXsjzDIPoe8y3511cyPk1AiCMQ4oj5DtALoK+4AQYHfALaYBdH6m2UnQAAAABJRU5ErkJggg=='));

    }
    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $converter = new Banner();

        $this->assertInstanceOf('\Goteo\Model\Banner', $converter);

        return $converter;
    }

    public function testCreate() {
        self::$data['project'] = get_test_project()->id;
        self::$data['node'] = get_test_node()->id;
        $ob = new Banner(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $ob = Banner::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Banner', $ob);

        $this->assertEquals($ob->project, self::$data['project']);
        $this->assertInstanceOf('\Goteo\Model\Image', $ob->image);
        $this->assertEquals($ob->image->name, self::$data['image']);
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testSaveLanguages($ob) {
        $errors = [];
        $this->assertTrue($ob->setLang('ca', self::$trans_data, $errors), print_r($errors, 1));
        return $ob;
    }

    /**
     * @depends testSaveLanguages
     */
    public function testCheckLanguages($ob) {
        $new = Banner::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\Banner', $new);
        $this->assertEquals(self::$data['title'], $new->title);
        $this->assertEquals(self::$data['description'], $new->description);
        Lang::set('ca');
        $new2 = Banner::get($ob->id);
        $this->assertEquals(self::$trans_data['title'], $new2->title);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testListing($ob) {
        $list = Banner::getAll(false, get_test_node()->id);
        $this->assertInternalType('array', $list);
        $new = $list[$ob->id];
        $this->assertInstanceOf('Goteo\Model\Banner', $new);
        $this->assertEquals(self::$data['title'], $new->title);
        $this->assertEquals(self::$data['description'], $new->description);

        Lang::set('ca');
        $list = Banner::getAll(false, get_test_node()->id);
        $this->assertInternalType('array', $list);
        $new2 = $list[$ob->id];
        $this->assertEquals(self::$trans_data['title'], $new2->title);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Banner::delete($ob->id));
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
    public static function tearDownAfterClass() {
        delete_test_project();
        delete_test_user();
        delete_test_node();
        unlink(self::$test_img);
    }
}
