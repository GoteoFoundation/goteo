<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Category;
use Goteo\Model\Sdg;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class CategoryTest extends \PHPUnit\Framework\TestCase {

    private static $data = ['name' => 'Test category', 'description' => 'Test description'];
    private static $trans_data = ['name' => 'Categoria test', 'description' => 'Descripció test'];
    private static $sdg;

    public static function setUpBeforeClass(): void {
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
    public function testSdgRelationships($ob) {
        $errors = [];
        $sdg = new Sdg(['name' => 'sdg test sdg']);
        $this->assertTrue($sdg->save($errors), implode("\n", $errors));
        $this->assertInstanceOf('\Goteo\Model\Category', $ob->addSdgs($sdg));
        $sdgs = $ob->getSdgs();
        $this->assertCount(1, $sdgs);
        $this->assertInstanceOf('\Goteo\Model\Sdg', $sdgs[0]);
        self::$sdg = $sdgs[0]->id;
        // repeated assignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Category', $ob->addSdgs($sdgs));
        $this->assertCount(1, $ob->getSdgs());
        //
        $this->assertInstanceOf('\Goteo\Model\Category', $ob->replaceSdgs($sdgs));
        $this->assertCount(1, $ob->getSdgs());

        return $ob;
    }

    /**
     * @depends testSdgRelationships
     */
    public function testRemoveSdgRelationships($ob) {
        $this->assertCount(1, $ob->getSdgs());
        $this->assertInstanceOf('\Goteo\Model\Category', $ob->removeSdgs(self::$sdg));
        $this->assertCount(0, $ob->getSdgs());
        // repeated unassignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Category', $ob->removeSdgs(self::$sdg));
        $this->assertCount(0, $ob->getSdgs());

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

    /**
     * Some cleanup
     */
    static function tearDownAfterClass(): void {
        Sdg::query("DELETE FROM sdg WHERE `id` = ?", self::$sdg);
    }

}
