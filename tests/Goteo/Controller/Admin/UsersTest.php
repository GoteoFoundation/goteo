<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Users;

class UsersTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Users();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Users', $controller);

        return $controller;
    }
}
