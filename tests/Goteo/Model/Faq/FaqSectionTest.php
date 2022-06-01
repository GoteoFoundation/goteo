<?php

namespace Goteo\Model\Tests;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Faq\FaqSection;
use Goteo\TestCase;

class FaqSectionTest extends TestCase
{
    private static array $data = [
        'name' => 'test-section',
        'slug' => 'test-section-slug',
        'button_action' => '',
        'button_url' => ''
    ];

    public function testInstance(): FaqSection
    {
        $faqSection = new FaqSection();

        $this->assertInstanceOf(FaqSection::class, $faqSection);

        return $faqSection;
    }

    /**
     * @depends testInstance
     */
    public function testValidate(FaqSection $faqSection)
    {
        $this->assertFalse($faqSection->validate());
        $this->assertFalse($faqSection->save());
    }

    public function testCreate(): FaqSection
    {
        $faqSection = new FaqSection(self::$data);
        $errors = [];

        $this->assertTrue($faqSection->validate($errors), implode(',', $errors));
        $this->assertTrue($faqSection->save($errors), implode(',', $errors));

        return $faqSection;
    }

    /**
     * @depends testCreate
     */
    public function testGetById(FaqSection $faqSection): void
    {
        $ob = FaqSection::getById($faqSection->id);

        $this->assertEquals($faqSection->id, $ob->id);
    }

    /**
     * @depends testCreate
     */
    public function testGetBySlug(FaqSection $faqSection): void
    {
        $ob = FaqSection::getBySlug($faqSection->slug);

        $this->assertEquals($faqSection->id, $ob->id);
    }

    /**
     * @depends testCreate
     */
    public function testGetListNotEmpty()
    {
        $list = FaqSection::getList();
        $this->assertIsArray($list);
        $this->assertNotEmpty($list);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(FaqSection $faqSection): FaqSection
    {
        $this->assertTrue($faqSection->dbDelete());

        return $faqSection;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting(FaqSection $faqSection)
    {
        $this->expectException(ModelNotFoundException::class);

        FaqSection::get($faqSection->id);
    }

    /**
     * @depends testDelete
     */
    public function getListEmpty()
    {
        $list = FaqSection::getList();
        $this->assertIsArray($list);
        $this->assertEmpty($list);
    }
}
