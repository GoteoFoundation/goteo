<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Glossary;

class GlossaryTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('title' => 'Test title', 'text' => 'Test text');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Glossary();

        $this->assertInstanceOf('\Goteo\Model\Glossary', $ob);

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
        $ob = new Glossary(self::$data);
        $this->assertTrue($ob->validate($errors));
        $this->assertTrue($ob->save());
        $ob = Glossary::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Glossary', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->delete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Glossary::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Glossary::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Glossary::delete($ob->id));
    }
}
