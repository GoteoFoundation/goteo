<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Home;

class HomeTest extends TestCase {

    private static $data = array('item' => 'test', 'type' => 'side', 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Home();

        $this->assertInstanceOf('\Goteo\Model\Home', $ob);

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
        self::$data['node'] = get_test_node()->id;
        $ob = new Home(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Home::get($ob->item, self::$data['node']);
        $this->assertInstanceOf('\Goteo\Model\Home', $ob);

        foreach(self::$data as $key => $val) {
            if($key === 'order') $this->assertEquals($ob->$key, $val + 1);
            else $this->assertEquals($ob->$key, $val);
        }

        //delete statically
        $this->assertTrue(Home::remove($ob->item, self::$data['node'], self::$data['type']));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Home::get($ob->item);
        $this->assertFalse($ob);
    }
}
