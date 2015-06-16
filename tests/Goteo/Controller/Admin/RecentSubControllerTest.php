<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\RecentSubController;

class RecentSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new RecentSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\RecentSubController', $controller);

        return $controller;
    }
}
