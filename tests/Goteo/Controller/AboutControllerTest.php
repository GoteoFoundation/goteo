<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\AboutController;

class AboutControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new AboutController();

        $this->assertInstanceOf('\Goteo\Controller\AboutController', $controller);

        return $controller;
    }
}
