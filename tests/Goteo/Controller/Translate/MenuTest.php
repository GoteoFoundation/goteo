<?php


namespace Goteo\Controller\Translate\Tests;

use Goteo\Controller\Translate\Menu;

class MenuTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Menu();

        $this->assertInstanceOf('\Goteo\Controller\Translate\Menu', $controller);

        return $controller;
    }
}
