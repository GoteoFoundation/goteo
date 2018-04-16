<?php


namespace Goteo\Model\Tests;

use Goteo\Model\License;

class LicenseTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('id' => 'test', 'name' => 'Test category', 'description' => 'Test description');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new License();

        $this->assertInstanceOf('\Goteo\Model\License', $ob);

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
        $ob = new License(self::$data);
        $errors = [];
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $ob = License::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\License', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(License::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = License::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(License::delete($ob->id));
    }
}
