<?php


namespace Goteo\Tests;

use Goteo\Library\Message;

class MessageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Message();

        $this->assertInstanceOf('\Goteo\Library\Message', $converter);

        return $converter;
    }
}
