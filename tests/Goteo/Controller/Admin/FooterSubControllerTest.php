<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\FooterSubController;

class FooterSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new FooterSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\FooterSubController', $controller);

        return $controller;
    }
}
