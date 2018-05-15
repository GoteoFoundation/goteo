<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Log;
use Goteo\Application\Session;

class LogTest extends TestCase {
    private static $data = ['scope' => 'test', 'target_type' => 'user', 'text' => 'Log test text'];

    public function testInstance() {
        $ob = new Log();
        $this->assertInstanceOf('\Goteo\Model\Log', $ob);

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
        $ob = new Log(self::$data);
        $errors = [];
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Log::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Log', $ob);

        foreach(self::$data as $key => $val) {
            if($key === 'order') $this->assertEquals($ob->$key, $val + 1);
            else $this->assertEquals($ob->$key, $val);
        }
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());
        return $ob;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting($ob) {
        $ob = Log::get($ob->id);
        $this->assertNull($ob);
    }

    /**
     * @depends testDelete
     */
    public function testAppend($ob) {
        Session::setUser(get_test_user());
        try {
            Log::append();
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\ModelException', $e);
        }
        $log = Log::append(self::$data);
        $this->assertInstanceOf('Goteo\Model\Log', $log);
        $this->assertTrue($log->dbDelete());
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_user();
        delete_test_project();
        delete_test_node();
    }
}
