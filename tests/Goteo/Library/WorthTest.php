<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Worth;

class WorthTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Worth();

        $this->assertInstanceOf('\Goteo\Library\Worth', $converter);

        return $converter;
    }
}
