<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Check;

class CheckTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Check();

        $this->assertInstanceOf('\Goteo\Library\Check', $converter);

        return $converter;
    }
}
