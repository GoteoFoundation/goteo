<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalDocumentType;
use Goteo\TestCase;

class LegalDocumentTypeTest extends TestCase {

    public function testInstance(): LegalDocumentType
    {
        $ob = LegalDocumentType::create();

        $this->assertInstanceOf(LegalDocumentType::class, $ob);

        return $ob;
    }
}