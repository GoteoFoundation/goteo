<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\IconsSubController;

class IconsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new IconsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\IconsSubController', $controller);

        return $controller;
    }
}
