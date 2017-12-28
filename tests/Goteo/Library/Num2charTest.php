<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Num2char;

class Num2charTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Num2char(10, 1);

        $this->assertInstanceOf('\Goteo\Library\Num2char', $converter);

        return $converter;
    }
}
