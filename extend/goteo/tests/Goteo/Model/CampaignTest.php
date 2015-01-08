<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Campaign;

class CampaignTest extends \PHPUnit_Framework_TestCase {


    private static $data = array('node' => 'test', 'call' => 'test', 'order' => 0, 'active' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Campaign();

        $this->assertInstanceOf('\Goteo\Model\Campaign', $ob);

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
        $ob = new Campaign(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));

        //TODO: create call
        // $ob = Campaign::get($ob->id);
        // $this->assertInstanceOf('\Goteo\Model\Campaign', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Campaign::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Campaign::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Campaign::delete($ob->id));
    }
}
