<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Criteria;

class CriteriaTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('section' => 'test', 'title' => 'Test title', 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $converter = new Criteria();

        $this->assertInstanceOf('\Goteo\Model\Criteria', $converter);

        return $converter;
    }
    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    public function testCreate() {
        $ob = new Criteria(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Criteria::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Criteria', $ob);

        foreach(self::$data as $key => $val) {
            if($key === 'order') $this->assertEquals($ob->$key, $val + 1);
            else $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Criteria::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Criteria::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Criteria::delete($ob->id));
    }
}
