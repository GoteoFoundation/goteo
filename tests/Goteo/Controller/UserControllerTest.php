<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\UserController;

class UserControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new UserController();

        $this->assertInstanceOf('\Goteo\Controller\UserController', $controller);

        return $controller;
    }
}
