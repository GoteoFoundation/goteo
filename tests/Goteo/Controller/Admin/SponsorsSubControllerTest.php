<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\SponsorsSubController;

class SponsorsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new SponsorsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\SponsorsSubController', $controller);

        return $controller;
    }
}
