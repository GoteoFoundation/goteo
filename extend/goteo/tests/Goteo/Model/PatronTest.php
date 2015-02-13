<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Patron;

class PatronTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('node' => 'test', 'project' => 'test', 'user' => 'test', 'title' => 'test title', 'description' => 'test description', 'active' => 0, 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Patron();

        $this->assertInstanceOf('\Goteo\Model\Patron', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate(), print_r($errors, 1));
        $this->assertFalse($ob->save());
    }

    public function testCreate() {
        $ob = new Patron(self::$data);

        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());
        //TODO: create project
        // $ob = Patron::get($ob->id);
        // $this->assertInstanceOf('\Goteo\Model\Patron', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Patron::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {

        $ob = Patron::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Patron::delete($ob->id));
    }
}
