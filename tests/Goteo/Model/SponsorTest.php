<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Sponsor;

class SponsorTest extends TestCase {

    private static $data = array('name' => 'Test Sponsor', 'url' => 'http://goteo.org', 'order' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Sponsor();

        $this->assertInstanceOf('\Goteo\Model\Sponsor', $ob);

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
        $ob = new Sponsor(self::$data);
        $errors = [];
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $ob = Sponsor::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Sponsor', $ob);

        foreach(self::$data as $key => $val) {
            if($key === 'order') $this->assertEquals($ob->$key, $val + 1);
            else $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Sponsor::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Sponsor::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Sponsor::delete($ob->id));
    }
    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_node();
    }
}
