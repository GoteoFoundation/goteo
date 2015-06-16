<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\BazarSubController;

class BazarSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new BazarSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\BazarSubController', $controller);

        return $controller;
    }
}
