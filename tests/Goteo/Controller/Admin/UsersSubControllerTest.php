<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\UsersSubController;

class UsersSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new UsersSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\UsersSubController', $controller);

        return $controller;
    }
}
