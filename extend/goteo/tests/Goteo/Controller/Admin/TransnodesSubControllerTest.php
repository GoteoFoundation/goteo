<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TransnodesSubController;

class TransnodesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TransnodesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\TransnodesSubController', $controller);

        return $controller;
    }
}
