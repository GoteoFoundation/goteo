<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Message;
use Goteo\Model\User;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class MessageTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('message' => 'Test message content');
    private static $trans_data = array('message' => 'Test contingut del missatge');
    private static $user_data = array('userid' => 'test', 'name' => 'Test', 'email' => 'test@goteo.org', 'password' => 'testtest', 'active' => true);

    public static function setUpBeforeClass() {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

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

        $user = get_test_user();
        $p = get_test_project();

        $this->assertInstanceOf('\Goteo\Model\User', $user, print_r($errors, 1));

        $ob = new Message(self::$data + array('project' => $p->id, 'user' => $user->id));
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
        $new = Message::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\Message', $new);
        $this->assertEquals(self::$data['message'], $new->message);
        Lang::set('ca');
        $new2 = Message::get($ob->id, 'ca');
        $this->assertEquals(self::$trans_data['message'], $new2->message);
        Config::set('lang', 'es');
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testListing($ob) {
        $list = Message::getAll(get_test_project());
        $this->assertInternalType('array', $list);
        $new = $list[$ob->id];
        $this->assertInstanceOf('Goteo\Model\Message', $new);
        $this->assertEquals(self::$data['message'], $new->message);

        Lang::set('ca');
        $list = Message::getAll(get_test_project());
        $this->assertInternalType('array', $list);
        $new2 = $list[$ob->id];
        $this->assertEquals(self::$trans_data['message'], $new2->message);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Message::delete($ob->id));

    }

    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Message::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Message::delete($ob->id));
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }

}
