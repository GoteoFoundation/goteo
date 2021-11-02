<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalEntity;
use Goteo\Model\LegalEntity\LegalPerson;
use Goteo\TestCase;

class LegalPersonTest extends TestCase {

    public function testInstance(): LegalPerson
    {
        $ob = new LegalPerson();

        $this->assertInstanceOf(LegalPerson::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetEntity(LegalPerson $legalPerson) {
        $this->assertEquals(LegalEntity::LEGAL_PERSON, $legalPerson->getEntity());
    }
}