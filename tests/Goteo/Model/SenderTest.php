<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Sender;

class SenderTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Sender();

        $this->assertInstanceOf('\Goteo\Model\Sender', $converter);

        return $converter;
    }
}
