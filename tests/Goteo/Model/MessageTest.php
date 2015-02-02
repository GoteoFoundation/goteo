<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Message,
    Goteo\Model\User;

class MessageTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('message' => 'Test message content', 'project' => 'test');
    private static $user_data = array('userid' => 'test', 'name' => 'Test', 'email' => 'test@goteo.org', 'password' => 'testtest', 'active' => true);

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

        //Creates the user first
        if(!($user = User::getByEmail(self::$user_data['email']))) {
            echo "Creating user [test]\n";
            $user = new User(self::$user_data);
            $this->assertTrue($user->save($errors, array('active')), print_r($errors, 1));
            $user = User::getByEmail(self::$user_data['email']);
        }

        $this->assertInstanceOf('\Goteo\Model\User', $user, print_r($errors, 1));

        $ob = new Message(self::$data + array('user' => $user->id));
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));

        $ob = Message::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Message', $ob);

        foreach(self::$data as $key => $val) {
            if($key === 'user') {
                $this->assertInstanceOf('\Goteo\Model\User', $ob->$key, print_r($ob->$key, 1));
            }
            else $this->assertEquals($ob->$key, $val);
        }

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
