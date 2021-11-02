<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalDocumentType;
use Goteo\Model\LegalDocumentType\VAT;
use Goteo\TestCase;

class VATTest extends TestCase {

    public function testInstance(): VAT
    {
        $ob = new VAT();

        $this->assertInstanceOf(VAT::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetDocumentType(VAT $vat) {
        $this->assertEquals(LegalDocumentType::VAT, $vat->getDocumentType());
    }
}