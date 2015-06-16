<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TemplatesSubController;

class TemplatesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TemplatesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\TemplatesSubController', $controller);

        return $controller;
    }
}
