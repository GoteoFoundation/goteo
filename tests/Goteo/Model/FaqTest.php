<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Faq;

class FaqTest extends TestCase {

    private static $data = array('section' => 'test-section', 'description' => 'test description', 'title' => 'Test title', 'order' => 0);

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
        self::$data['node'] = get_test_node()->id;
        $ob = new Faq(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Faq::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Faq', $ob);

        foreach(self::$data as $key => $val) {
            if($key === 'order') $this->assertEquals($ob->$key, $val + 1);
            else $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $ob = new Faq(self::$data);
        $this->assertTrue($ob->save());
        $this->assertTrue(Faq::remove($ob->id, self::$data['node']));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Faq::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Faq::remove($ob->id));
    }
    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_node();
    }
}
