<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\ImageController;

class ImageControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new ImageController();

        $this->assertInstanceOf('\Goteo\Controller\ImageController', $controller);

        return $controller;
    }
}
