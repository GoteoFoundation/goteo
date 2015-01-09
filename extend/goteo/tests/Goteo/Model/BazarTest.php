<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Bazar;

class BazarTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Bazar();

        $this->assertInstanceOf('\Goteo\Model\Bazar', $converter);

        return $converter;
    }
}
