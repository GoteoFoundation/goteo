<?php

namespace Goteo\Model\Tests;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Core\DB;
use Goteo\Model\Faq;
use Goteo\Model\Faq\FaqSection;
use Goteo\Model\Faq\FaqSubsection;
use Goteo\TestCase;

class FaqTest extends TestCase {

    private static array $sectionData = [
        'name' => 'test-section',
        'slug' => 'test-section-slug',
        'button_action' => '',
        'button_url' => ''
    ];

    private static array $subsectionData = [
        'name' => 'test-subsection'
    ];

    private static array $data = ['description' => 'test description', 'title' => 'Test title', 'order' => 1];
    private static array $trans_data = ['description' => 'Descripció test', 'title' => 'Test títol'];

    public static function setUpBeforeClass(): void
    {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

    public function testInstance(): Faq
    {
        DB::cache(false);

        $ob = new Faq();

        $this->assertInstanceOf(Faq::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate(Faq $ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    public function testCreate(): Faq {

        $errors = [];
        $faqSection = new FaqSection(self::$sectionData);
        $this->assertTrue($faqSection->save($errors), implode(',', $errors));

        $faqSubsection = new FaqSubsection(self::$subsectionData);
        $faqSubsection->section_id = $faqSection->id;
        $this->assertTrue($faqSubsection->save($errors), implode(',', $errors));

        self::$data['node'] = get_test_node()->id;
        self::$data['subsection_id'] = $faqSubsection->id;
        $ob = new Faq(self::$data);
        $this->assertTrue($ob->validate($errors), implode(',',$errors));
        $this->assertTrue($ob->save($errors), implode(',', $errors));

        $ob = Faq::getById($ob->id);
        $this->assertInstanceOf(Faq::class, $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($val, $ob->{$key});
        }
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testSaveLanguages(Faq $ob): Faq {
        $errors = [];
        $this->assertTrue($ob->setLang('ca', self::$trans_data, $errors), print_r($errors, 1));
        return $ob;
    }

    /**
     * @depends testSaveLanguages
     */
    public function testCheckLanguages(Faq $ob) {
        $faq = Faq::getById($ob->id);
        $this->assertInstanceOf(Faq::class, $faq);
        $this->assertEquals(self::$data['title'], $faq->title);
        $this->assertEquals(self::$data['description'], $faq->description);
        Lang::set('ca');
        $faq2 = Faq::getById($ob->id);
        $this->assertEquals(self::$trans_data['title'], $faq2->title);
        $this->assertEquals(self::$trans_data['description'], $faq2->description);
        Lang::set('es');

    }

    /**
     * @depends testCreate
     */
    public function testListing(Faq $faq) {
        $list = Faq::getAll(self::$data['subsection_id']);
        $this->assertIsArray($list);

        $list = Faq::getAll($faq->subsection_id);
        $this->assertCount(1, $list);
        $listed_faq = current($list);
        $this->assertInstanceOf(Faq::class, $listed_faq);
        $this->assertEquals($faq->title, $listed_faq->title);
        $this->assertEquals($faq->description, $listed_faq->description);

        Lang::set('ca');
        $list = Faq::getAll(self::$data['subsection_id']);
        $this->assertIsArray($list);
        $listed_faq2 = current($list);
        $this->assertEquals(self::$trans_data['title'], $listed_faq2->title);
        $this->assertEquals(self::$trans_data['description'], $listed_faq2->description);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Faq $ob): Faq
    {
        $this->assertTrue($ob->dbDelete());

        $subsection = $ob->subsection_id;
        //save and delete statically
        $ob = new Faq(self::$data);
        $ob->subsection_id = $subsection;

        $errors = [];
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $this->assertTrue(Faq::remove($ob->id, self::$data['node']));

        return $ob;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting(Faq $ob) {
        $this->expectException(ModelNotFoundException::class);
        $ob = Faq::get($ob->id);
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass(): void {
        delete_test_node();
        self::delete_faq_section();
    }

    private static  function delete_faq_section(): void
    {
        $faqSection = FaqSection::getBySlug(self::$sectionData['slug']);
        $faqSection->dbDelete();
    }

}
