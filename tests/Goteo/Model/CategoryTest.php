<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Category;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class CategoryTest extends \PHPUnit_Framework_TestCase {

    private static $data = ['name' => 'Test category', 'description' => 'Test description'];
    private static $trans_data = ['name' => 'Categoria test', 'description' => 'Descripció test'];

    public static function setUpBeforeClass() {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Category();

        $this->assertInstanceOf('\Goteo\Model\Category', $ob);

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
        $ob = new Category(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->save($errors), print_r($errors, 1));
        $ob = Category::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Category', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
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
        $cat = Category::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\Category', $cat);
        $this->assertEquals(self::$data['name'], $cat->name);
        $this->assertEquals(self::$data['description'], $cat->description);
        Lang::set('ca');

        $cat2 = Category::get($ob->id);
        $this->assertEquals(self::$trans_data['name'], $cat2->name);
        $this->assertEquals(self::$trans_data['description'], $cat2->description);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $this->assertTrue($ob->save());
        $this->assertTrue(Category::delete($ob->id));

        return $ob;
    }


    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Category::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Category::delete($ob->id));
    }

}
