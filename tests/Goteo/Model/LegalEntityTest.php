<?php

namespace Goteo\Model\Tests;

use Goteo\Model\LegalEntity;
use Goteo\TestCase;

class LegalEntityTest extends TestCase {

    public function testInstance(): LegalEntity
    {
        $ob = LegalEntity::create();

        $this->assertInstanceOf(LegalEntity::class, $ob);

        return $ob;
    }
}