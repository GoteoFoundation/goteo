<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Dashboard;

class DashboardTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Dashboard();

        $this->assertInstanceOf('\Goteo\Controller\Dashboard', $controller);

        return $controller;
    }
}
