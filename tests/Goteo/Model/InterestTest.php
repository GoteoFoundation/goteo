<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Interest;

class InterestTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Interest();

        $this->assertInstanceOf('\Goteo\Model\Interest', $converter);

        return $converter;
    }
}
