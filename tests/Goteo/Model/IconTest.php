<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Icon;

class IconTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('id' => 'test', 'name' => 'Test category', 'description' => 'Test description', 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Icon();

        $this->assertInstanceOf('\Goteo\Model\Icon', $ob);

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
        $ob = new Icon(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Icon::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Icon', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Icon::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Icon::delete($ob->id));
    }

}
