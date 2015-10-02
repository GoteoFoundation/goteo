<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\IndexController;

class IndexControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new IndexController();

        $this->assertInstanceOf('\Goteo\Controller\IndexController', $controller);

        return $controller;
    }
}
