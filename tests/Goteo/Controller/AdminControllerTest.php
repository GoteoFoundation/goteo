<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\AdminController;

class AdminControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new AdminController();

        $this->assertInstanceOf('\Goteo\Controller\AdminController', $controller);

        return $controller;
    }
}
