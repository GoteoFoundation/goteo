<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NewsSubController;

class NewsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new NewsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\NewsSubController', $controller);

        return $controller;
    }
}
