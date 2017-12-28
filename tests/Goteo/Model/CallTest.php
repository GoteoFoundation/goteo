<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Call;

class CallTest extends \PHPUnit_Framework_TestCase {
    private static $data = array('id' => 'test-project', 'owner' => 'test');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Call();

        $this->assertInstanceOf('\Goteo\Model\Call', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }
/*
    public function testCreate() {
        $ob = new Call(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->create());

        $ob = Call::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Call', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        return $ob;
    }
    */
    public function testNonExisting() {
        try {
            $ob = Call::get('non-existing-call');
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelNotFoundException', $e);

        }
    }
}
