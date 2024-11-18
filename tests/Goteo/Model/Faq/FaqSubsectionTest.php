<?php

namespace Goteo\Model\Tests;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Faq\FaqSection;
use Goteo\Model\Faq\FaqSubsection;
use Goteo\TestCase;

class FaqSubsectionTest extends TestCase
{
    private static array $data = [
        'name' => 'test-subsection'
    ];

    private static array $sectionData = [
        'name' => 'test-section',
        'slug' => 'test-section-slug',
        'button_action' => '',
        'button_url' => ''
    ];

    public function testInstance(): FaqSubsection
    {
        $faqSubsection = new FaqSubsection();

        $this->assertInstanceOf(FaqSubsection::class, $faqSubsection);

        return $faqSubsection;
    }

    /**
     * @depends testInstance
     */
    public function testValidate(FaqSubsection $faqSubsection)
    {
        $this->assertFalse($faqSubsection->validate());
        $this->assertFalse($faqSubsection->save());
    }

    public function testCreate(): FaqSubsection
    {

        $faqSection = new FaqSection(self::$sectionData);
        $faqSection->save();

        $faqSubsection = new FaqSubsection(self::$data);
        $faqSubsection->section_id = $faqSection->id;
        $errors = [];

        $this->assertTrue($faqSubsection->validate($errors), implode(',', $errors));
        $this->assertTrue($faqSubsection->save($errors), implode(',', $errors));

        return $faqSubsection;
    }

    /**
     * @depends testCreate
     */
    public function testGet(FaqSubsection $faqSubsection): void
    {
        $ob = FaqSubsection::get($faqSubsection->id);

        $this->assertInstanceOf(FaqSubsection::class, $faqSubsection);
        $this->assertEquals($faqSubsection->id, $ob->id);
    }

    /**
     * @depends testCreate
     */
    public function testGetListNotEmpty()
    {
        $list = FaqSubsection::getList();
        $this->assertIsArray($list);
        $this->assertNotEmpty($list);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(FaqSubsection $faqSubsection): FaqSubsection
    {
        $this->assertTrue($faqSubsection->dbDelete());

        return $faqSubsection;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting(FaqSubsection $faqSubsection)
    {
        $this->expectException(ModelNotFoundException::class);

        FaqSubsection::get($faqSubsection->id);
    }

    /**
     * @depends testDelete
     */
    public function getListEmpty()
    {
        $list = FaqSubsection::getList();
        $this->assertIsArray($list);
        $this->assertEmpty($list);
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass(): void {
        self::delete_faq_section();
    }

    private static function delete_faq_section()
    {
        $faqSection = FaqSection::getBySlug(self::$sectionData['slug']);
        $faqSection->dbDelete();
    }
}
