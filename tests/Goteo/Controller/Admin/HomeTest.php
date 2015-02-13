<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Home;

class HomeTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Home();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Home', $controller);

        return $controller;
    }
}
