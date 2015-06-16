<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\InfoSubController;

class InfoSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new InfoSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\InfoSubController', $controller);

        return $controller;
    }
}
