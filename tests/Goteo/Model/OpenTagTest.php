<?php


namespace Goteo\Model\Tests;

use Goteo\Model\OpenTag;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class OpenTagTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('name' => 'test name', 'description' => 'test description', 'order' => 0);
    private static $trans_data = array('name' => 'nom de test', 'description' => 'descripció test');

    public static function setUpBeforeClass() {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new OpenTag();

        $this->assertInstanceOf('\Goteo\Model\OpenTag', $ob);

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
        $ob = new OpenTag(self::$data);

        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save());
        $ob = OpenTag::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\OpenTag', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
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
        $new = OpenTag::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\OpenTag', $new);
        $this->assertEquals(self::$data['name'], $new->name);
        $this->assertEquals(self::$data['description'], $new->description);
        Lang::set('ca');
        $new2 = OpenTag::get($ob->id);
        $this->assertEquals(self::$trans_data['name'], $new2->name);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testListing($ob) {
        $list = OpenTag::getAll();
        $this->assertInternalType('array', $list);
        $new = $list[$ob->id];
        $this->assertInstanceOf('Goteo\Model\OpenTag', $new);
        $this->assertEquals(self::$data['name'], $new->name);
        $this->assertEquals(self::$data['description'], $new->description);

        Lang::set('ca');
        $list = OpenTag::getAll();
        $this->assertInternalType('array', $list);

        $new2 = $list[$ob->id];
        $this->assertEquals(self::$trans_data['name'], $new2->name);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');
    }
    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(OpenTag::delete($ob->id));

        return $ob;
    }
    /**
     * @depends testDelete
     */
    public function testNonExisting($ob) {
        $ob = OpenTag::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(OpenTag::delete($ob->id));
    }
}
