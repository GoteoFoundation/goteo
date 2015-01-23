<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Message;

class MessageTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('user' => 'test', 'message' => 'Test message content', 'project' => 'test');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Message();

        $this->assertInstanceOf('\Goteo\Model\Message', $ob);

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
        $ob = new Message(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());
        //TODO: create a user first
        // $ob = Message::get($ob->id);
        // $this->assertInstanceOf('\Goteo\Model\Message', $ob);

        // foreach(self::$data as $key => $val) {
        //     if($key === 'user') {
        //         $this->assertInstanceOf('\Goteo\Model\User', $ob->$key, print_r($ob->$key, 1));
        //     }
        //     else $this->assertEquals($ob->$key, $val);
        // }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Message::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Message::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Message::delete($ob->id));
    }
}
