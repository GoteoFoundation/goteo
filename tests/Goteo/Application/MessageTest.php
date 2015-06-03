<?php


namespace Goteo\Library\Tests;

use Goteo\Application\Message;

class MessageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Message();

        $this->assertInstanceOf('\Goteo\Application\Message', $converter);

        return $converter;
    }
}
