<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Interest;

class InterestTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Interest();

        $this->assertInstanceOf('\Goteo\Model\User\Interest', $converter);

        return $converter;
    }
}
