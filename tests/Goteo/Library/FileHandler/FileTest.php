<?php

namespace Goteo\Library\Tests;

use Goteo\Model;
use Goteo\Library\FileHandler\File;
use Goteo\Library\FileHandler\S3File;
use Goteo\Library\FileHandler\LocalFile;

class FileTest extends \PHPUnit_Framework_TestCase {
    protected static $handler = 'local';
    protected static $test_img ;
    protected static $path;
    protected static $start_time = 0;

    //read config
    public static function setUpBeforeClass() {

        if(defined('FILE_HANDLER') && FILE_HANDLER == 's3') self::$handler = 's3';

        //a freak path
        self::$path = uniqid('please-delete-me-');

        //usefull when testing file modification times
        self::$start_time = time();

        //temp file
        self::$test_img = __DIR__ . '/test.png';
        file_put_contents(self::$test_img, base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABkElEQVRYhe3Wv2qDQBgA8LxJH8BXcHLN4pCgBxIOddAlSILorFDaQRzFEHEXUWyXlo6BrkmeI32Hr1PTMyb1rtpIIQff6vdTvz83unt+giFjdAP8awCXZ8Dl2XCAcRjAOAyGA8iaDrKmDwMQ4ggQUgAhBYQ4uj5AMswjQDLM6wJE3zsm/wrR964D4NOkkbzLr2AC8GkC8gxfBMgzDHya/A2AyzOQNf1i8iNC05lmAxWAy7Na0bWFZJjUCCrAdLmoJbDmFlRFCe+bDVhz6yxiulz0AyD7HSEFHu8fgDyu7XQqylbAxP1O4NoOnB6M1YuAiet0B5CF9/by2gC0FWRnAPnAj8OBCYCQ0i+A9vQKIAfPfrtrTb7f7mqDqTOAbMF1vGoFrOMVUyu2AsZhUPukP30F8u0RUqguK1SDiJyCGKtQFWUjeVWUtZakXdFUgHNLCGMVXNsB13Yas4BlKVEvIz5NqJcRy0ZkWsdcnoHoe2dXsjzDIPoe8y3511cyPk1AiCMQ4oj5DtALoK+4AQYHfALaYBdH6m2UnQAAAABJRU5ErkJggg=='));

    }

    /**
     * Ensures that the correct class handles the file management
     */
    public function testFactory() {
        $fp = File::factory();
        $fp->connect();

        $fp->setPath(self::$path);
        if(self::$handler === 's3') {
            $this->assertTrue($fp instanceof S3File, 'NOTE: Are the constants AWS_KEY & AWS_SECRET defined?');
        }
        else {
            $this->assertTrue($fp instanceof LocalFile);
        }

        return $fp;
    }

    /**
     * Tests if a file does not exists in remote file system
     * @depends testFactory
     */
    public function testDontExistFirst($fp) {
        $this->assertFalse($fp->exists(self::$test_img));
        return $fp;
    }

   /**
    * @depends testDontExistFirst
    */
    public function testUpload($fp) {
        $this->assertTrue($fp->upload(self::$test_img, "test/img.png"));

        $this->assertFalse($fp->upload("i-dont-exist.png", "i-wont-exist.png"));
        return $fp;
    }

    /**
     * @depends testUpload
     */
    public function testPutGetContents($fp) {
        $msg = "this is a phpunit test";
        $this->assertEquals($fp->put_contents("contents.txt", $msg), strlen($msg));

        $this->assertEquals($fp->get_contents("contents.txt"), $msg);

        return $fp;
    }

     /**
     * @depends testPutGetContents
     */
    public function testExists($fp) {

        $this->assertTrue($fp->exists("test/img.png"));

        $this->assertTrue($fp->exists("contents.txt"));

        $this->assertFalse($fp->exists("i-dont-exist.png"));

        return $fp;
    }

    /**
     * @depends testExists
     */
    public function testGetSaveName($fp) {

        $this->assertEquals($fp->get_save_name("contents.txt"), "contents-1.txt");

        return $fp;
    }

    /**
     * @depends testGetSaveName
     */
    public function testFileModificationTime($fp) {

        $this->assertGreaterThan(self::$start_time - 1, $fp->mtime("test/img.png"));
        $this->assertGreaterThan(self::$start_time - 1, $fp->mtime("contents.txt"));

        $this->assertEquals($fp->mtime("i-dont-exist.png"), -1);

        return $fp;
    }

    /**
     * @depends testFileModificationTime
     */
    public function testDelete($fp) {

        $extra = array('auto_delete_dirs' => true);


        $this->assertTrue($fp->delete("contents.txt", $extra));

        $this->assertTrue($fp->delete("test/img.png", $extra));

        if(self::$handler === 'local') {
            //skipping this test in S3 because delete action always return true
            $this->assertFalse($fp->delete("i-dont-exist.png", $extra));

            //auto_delete_dirs must delete the dir
            $this->assertFalse(is_dir($fp->get_path('test/')));
            //cleaning up test dir
            rmdir($fp->get_path());
        }

        return $fp;
    }

    /**
     * Remove temporal files on finish
     */
    public static function tearDownAfterClass() {
        unlink(self::$test_img);
    }
}
