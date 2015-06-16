<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NodeSubController;

class NodeSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new NodeSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\NodeSubController', $controller);

        return $controller;
    }
}
