<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CallsSubController;

class CallsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new CallsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\CallsSubController', $controller);

        return $controller;
    }
}
