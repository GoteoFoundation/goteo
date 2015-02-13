<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Icons;

class IconsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Icons();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Icons', $controller);

        return $controller;
    }
}
