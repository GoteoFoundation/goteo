<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\BlogController;

class BlogControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new BlogController();

        $this->assertInstanceOf('\Goteo\Controller\BlogController', $controller);

        return $controller;
    }
}
