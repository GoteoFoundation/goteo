<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\WidgetController;

class WidgetControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new WidgetController();

        $this->assertInstanceOf('\Goteo\Controller\WidgetController', $controller);

        return $controller;
    }
}
