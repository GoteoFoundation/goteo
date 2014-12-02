<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\User;

class UserTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new User();

        $this->assertInstanceOf('\Goteo\Controller\User', $controller);

        return $controller;
    }
}
