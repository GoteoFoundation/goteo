<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Widget;

class WidgetTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Widget();

        $this->assertInstanceOf('\Goteo\Controller\Widget', $controller);

        return $controller;
    }
}
