<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Worth;

class WorthTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Worth();

        $this->assertInstanceOf('\Goteo\Library\Worth', $converter);

        return $converter;
    }
}
