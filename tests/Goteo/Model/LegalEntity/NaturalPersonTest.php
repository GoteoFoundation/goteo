<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalEntity;
use Goteo\Model\LegalEntity\NaturalPerson;
use Goteo\TestCase;

class NaturalPersonTest extends TestCase {

    public function testInstance(): NaturalPerson
    {
        $ob = new NaturalPerson();

        $this->assertInstanceOf(NaturalPerson::class, $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testGetEntity(NaturalPerson $naturalPerson) {
        $this->assertEquals(LegalEntity::NATURAL_PERSON, $naturalPerson->getEntity());
    }
}