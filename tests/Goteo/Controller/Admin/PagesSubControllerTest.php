<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PagesSubController;

class PagesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new PagesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\PagesSubController', $controller);

        return $controller;
    }
}
