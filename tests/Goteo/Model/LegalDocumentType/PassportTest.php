<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalDocumentType;
use Goteo\Model\LegalDocumentType\Passport;
use Goteo\TestCase;

class PassportTest extends TestCase {

    public function testInstance(): Passport
    {
        $ob = new Passport();

        $this->assertInstanceOf(Passport::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetDocumentType(Passport $passport) {
        $this->assertEquals(LegalDocumentType::PASSPORT, $passport->getDocumentType());
    }
}