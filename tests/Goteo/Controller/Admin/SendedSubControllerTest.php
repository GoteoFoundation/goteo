<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\SendedSubController;

class SendedSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new SendedSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\SendedSubController', $controller);

        return $controller;
    }
}
