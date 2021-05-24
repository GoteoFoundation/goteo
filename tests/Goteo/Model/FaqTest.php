<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Faq;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class FaqTest extends TestCase {

    private static $data = array('section' => 'test-section', 'description' => 'test description', 'title' => 'Test title', 'order' => 1);
    private static $trans_data = array('description' => 'Descripció test', 'title' => 'Test títol');

    public static function setUpBeforeClass(): void {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

    public function testInstance(): Faq
    {
        \Goteo\Core\DB::cache(false);

        $ob = new Faq();

        $this->assertInstanceOf('\Goteo\Model\Faq', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate(Faq $ob) {
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
            $this->assertEquals($ob->{$key}, $val);
        }
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testSaveLanguages(Faq $ob) {
        $errors = [];
        $this->assertTrue($ob->setLang('ca', self::$trans_data, $errors), print_r($errors, 1));
        return $ob;
    }

    /**
     * @depends testSaveLanguages
     */
    public function testCheckLanguages(Faq $ob) {
        $faq = Faq::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\Faq', $faq);
        $this->assertEquals(self::$data['title'], $faq->title);
        $this->assertEquals(self::$data['description'], $faq->description);
        Lang::set('ca');
        $faq2 = Faq::get($ob->id);
        $this->assertEquals(self::$trans_data['title'], $faq2->title);
        $this->assertEquals(self::$trans_data['description'], $faq2->description);
        Lang::set('es');

    }

    /**
     * @depends testCreate
     */
    public function testListing() {
        $list = Faq::getAll('test-section');
        $this->assertIsArray($list);
        $faq = end($list);
        $this->assertInstanceOf('Goteo\Model\Faq', $faq);
        $this->assertEquals(self::$data['title'], $faq->title);
        $this->assertEquals(self::$data['description'], $faq->description);

        Lang::set('ca');
        $list = Faq::getAll('test-section');
        $this->assertIsArray($list);
        $faq2 = end($list);
        $this->assertEquals(self::$trans_data['title'], $faq2->title);
        $this->assertEquals(self::$trans_data['description'], $faq2->description);
        Lang::set('es');
    }


    /**
     * @depends testCreate
     */
    public function testDelete(Faq $ob): Faq
    {
        $this->assertTrue($ob->dbDelete());

        //save and delete statically
        $ob = new Faq(self::$data);
        $errors = [];
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $this->assertTrue(Faq::remove($ob->id, self::$data['node']));

        return $ob;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting(Faq $ob) {
        $ob = Faq::get($ob->id);
        $this->assertFalse($ob);
        $this->assertFalse(Faq::remove($ob->id));
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass(): void {
        delete_test_node();
    }
}
