<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PromoteSubController;

class PromoteSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new PromoteSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\PromoteSubController', $controller);

        return $controller;
    }
}
