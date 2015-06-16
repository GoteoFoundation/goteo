<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NodesSubController;

class NodesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new NodesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\NodesSubController', $controller);

        return $controller;
    }
}
