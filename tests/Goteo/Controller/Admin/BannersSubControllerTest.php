<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\BannersSubController;

class BannersSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new BannersSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\BannersSubController', $controller);

        return $controller;
    }
}
