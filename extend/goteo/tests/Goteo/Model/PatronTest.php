<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Patron;

class PatronTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Patron();

        $this->assertInstanceOf('\Goteo\Model\Patron', $converter);

        return $converter;
    }
}
