<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalDocumentType;
use Goteo\Model\LegalDocumentType\NIE;
use Goteo\Model\LegalDocumentType\CIF;
use Goteo\Model\LegalDocumentType\NIF;
use Goteo\Model\LegalDocumentType\VAT;
use Goteo\Model\LegalDocumentType\Passport;
use Goteo\TestCase;

class LegalDocumentTypeTest extends TestCase {

    public function testInstance(): LegalDocumentType
    {
        $ob = LegalDocumentType::create();

        $this->assertInstanceOf(LegalDocumentType::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetDocumentType(LegalDocumentType $legalDocumentType) {
        $this->assertEquals(LegalDocumentType::NIF, $legalDocumentType->getDocumentType());
    }

    public function testGetLegalDocumentTypes() {
        $this->assertIsArray(LegalDocumentType::getLegalDocumentTypes());
    }

    /**
     * @dataProvider documentTypesDataProvider
     */
    public function testCreate($inputType, $expectedOutput) {
        $this->assertInstanceOf($expectedOutput, LegalDocumentType::create($inputType));
    }


    public function documentTypesDataProvider(): Iterable {
            yield [LegalDocumentType::CIF, CIF::class];
            yield [LegalDocumentType::NIF, NIF::class];
            yield [LegalDocumentType::VAT, VAT::class];
            yield [LegalDocumentType::PASSPORT, Passport::class];
    }
}