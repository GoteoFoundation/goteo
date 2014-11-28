<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Buzz;

class BuzzTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Buzz();

        $this->assertInstanceOf('\Goteo\Library\Buzz', $converter);

        return $converter;
    }
}
