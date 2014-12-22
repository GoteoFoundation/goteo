<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Call;

class CallTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Call();

        $this->assertInstanceOf('\Goteo\Model\Call', $converter);

        return $converter;
    }
}
