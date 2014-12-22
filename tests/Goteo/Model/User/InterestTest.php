<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Interest;

class InterestTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Interest();

        $this->assertInstanceOf('\Goteo\Model\User\Interest', $converter);

        return $converter;
    }
}
