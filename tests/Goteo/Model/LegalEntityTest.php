<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalEntity;
use Goteo\Model\LegalEntity\NaturalPerson;
use Goteo\Model\LegalEntity\LegalPerson;
use Goteo\TestCase;

class LegalEntityTest extends TestCase {

    public function testInstance(): LegalEntity
    {
        $ob = LegalEntity::create();

        $this->assertInstanceOf(LegalEntity::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetEntity(LegalEntity $legalEntity) {
        $this->assertEquals(LegalEntity::NATURAL_PERSON, $legalEntity->getEntity());
    }

    public function testGetNaturalPerson() {
        $this->assertEquals(LegalEntity::NATURAL_PERSON, LegalEntity::getNaturalPerson());
    }

    public function testGetLegalPerson() {
        $this->assertEquals(LegalEntity::LEGAL_PERSON, LegalEntity::getLegalPerson());
    }

    public function testLegalEntities() {
        $this->assertIsArray(LegalEntity::getLegalEntities());
    }

    /**
     * @dataProvider legalEntitiesDataProvider
     */
    public function testCreate($inputType, $expectedOutput) {
        $this->assertInstanceOf($expectedOutput, LegalEntity::create($inputType));
    }

    public function legalEntitiesDataProvider(): Iterable {
            yield [LegalEntity::NATURAL_PERSON, NaturalPerson::class];
            yield [LegalEntity::LEGAL_PERSON, LegalPerson::class];
    }
}