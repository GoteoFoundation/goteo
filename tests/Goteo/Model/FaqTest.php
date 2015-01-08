<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Faq;

class FaqTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('node' => 'test', 'section' => 'test-section', 'description' => 'test description', 'title' => 'Test title', 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Faq();

        $this->assertInstanceOf('\Goteo\Model\Faq', $ob);

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
        $ob = new Faq(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Faq::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Faq', $ob);

        foreach(self::$data as $key => $val) {
            if($key === 'order') $this->assertEquals($ob->$key, $val + 1);
            else $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $ob = new Faq(self::$data);
        $this->assertTrue($ob->save());
        $this->assertTrue(Faq::delete($ob->id, self::$data['node']));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Faq::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Faq::delete($ob->id));
    }
}
