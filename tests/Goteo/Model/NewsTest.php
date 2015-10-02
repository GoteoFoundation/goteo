<?php


namespace Goteo\Model\Tests;

use Goteo\Model\News;

class NewsTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('title' => 'Test news', 'url' => 'http://goteo.org', 'order' => 1);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new News();

        $this->assertInstanceOf('\Goteo\Model\News', $ob);

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
        $ob = new News(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = News::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\News', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(News::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = News::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(News::delete($ob->id));
    }
}
