<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\HomeSubController;

class HomeSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new HomeSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\HomeSubController', $controller);

        return $controller;
    }
}
