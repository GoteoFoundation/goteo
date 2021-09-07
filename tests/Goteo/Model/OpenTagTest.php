<?php


namespace Goteo\Model\Tests;

use Goteo\Core\DB;
use Goteo\Model\OpenTag;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class OpenTagTest extends \PHPUnit\Framework\TestCase {

    private static $data = array('name' => 'test name', 'description' => 'test description', 'order' => 0);
    private static $trans_data = array('name' => 'nom de test', 'description' => 'descripció test');

    public static function setUpBeforeClass(): void {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

    public function testInstance(): OpenTag
    {
        DB::cache(false);

        $ob = new OpenTag();

        $this->assertInstanceOf('\Goteo\Model\OpenTag', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate(OpenTag $ob) {
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
    public function testSaveLanguages(OpenTag $ob): OpenTag
    {
        $errors = [];
        $this->assertTrue($ob->setLang('ca', self::$trans_data, $errors), print_r($errors, 1));
        return $ob;
    }

    /**
     * @depends testSaveLanguages
     */
    public function testCheckLanguages(OpenTag $ob) {
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
    public function testListing(OpenTag $ob) {
        $list = OpenTag::getAll();
        $this->assertIsArray($list);
        $new = $list[$ob->id];
        $this->assertInstanceOf('Goteo\Model\OpenTag', $new);
        $this->assertEquals(self::$data['name'], $new->name);
        $this->assertEquals(self::$data['description'], $new->description);

        Lang::set('ca');
        $list = OpenTag::getAll();
        $this->assertIsArray($list);

        $new2 = $list[$ob->id];
        $this->assertEquals(self::$trans_data['name'], $new2->name);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testDelete(OpenTag $ob): OpenTag
    {
        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(OpenTag::delete($ob->id));

        return $ob;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting(OpenTag $ob) {
        $ob = OpenTag::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(OpenTag::delete($ob->id));
    }
}
