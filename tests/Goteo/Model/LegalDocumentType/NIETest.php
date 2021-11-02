<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalDocumentType\NIE;
use Goteo\TestCase;

class NIETest extends TestCase {

    public function testInstance(): NIE
    {
        $ob = new NIE();

        $this->assertInstanceOf(NIE::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetDocumentType(NIE $nie) {
        $this->assertEquals(NIE::NIE, $nie->getDocumentType());
    }
}