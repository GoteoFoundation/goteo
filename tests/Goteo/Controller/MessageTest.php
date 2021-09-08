<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Message;

class MessageTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new Message();

        $this->assertInstanceOf('\Goteo\Controller\Message', $controller);

        return $controller;
    }
}
