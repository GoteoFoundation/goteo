<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalDocumentType\CIF;
use Goteo\TestCase;

class CIFTest extends TestCase {

    public function testInstance(): CIF
    {
        $ob = new CIF();

        $this->assertInstanceOf(CIF::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetDocumentType(CIF $cif) {
        $this->assertEquals(CIF::CIF, $cif->getDocumentType());
    }
}