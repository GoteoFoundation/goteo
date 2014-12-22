<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Message;

class MessageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Message();

        $this->assertInstanceOf('\Goteo\Model\Message', $converter);

        return $converter;
    }
}
