<?php


namespace Goteo\Command\Tests;

use Goteo\Command\UsersSend;

class UsersSendTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new UsersSend();

        $this->assertInstanceOf('\Goteo\Command\UsersSend', $controller);

        return $controller;
    }
}
