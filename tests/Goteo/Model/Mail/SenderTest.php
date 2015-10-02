<?php


namespace Goteo\Model\Mail\Tests;

use Goteo\Model\Mail\Sender;

class SenderTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Sender();

        $this->assertInstanceOf('\Goteo\Model\Mail\Sender', $converter);

        return $converter;
    }
}
