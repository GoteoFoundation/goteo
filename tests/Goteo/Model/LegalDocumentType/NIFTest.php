<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalDocumentType;
use Goteo\Model\LegalDocumentType\NIF;
use Goteo\TestCase;

class NIFTest extends TestCase {

    public function testInstance(): NIF
    {
        $ob = new NIF();

        $this->assertInstanceOf(NIF::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetDocumentType(NIF $nif) {
        $this->assertEquals(LegalDocumentType::NIF, $nif->getDocumentType());
    }
}