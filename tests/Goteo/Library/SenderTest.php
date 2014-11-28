<?php


namespace Goteo\Tests;

use Goteo\Library\Sender;

class SenderTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Sender();

        $this->assertInstanceOf('\Goteo\Library\Sender', $converter);

        return $converter;
    }
}
