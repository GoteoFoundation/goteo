<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Buzz;

class BuzzTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Buzz();

        $this->assertInstanceOf('\Goteo\Library\Buzz', $converter);

        return $converter;
    }
}
