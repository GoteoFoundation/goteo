<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\BlogSubController;

class BlogSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new BlogSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\BlogSubController', $controller);

        return $controller;
    }
}
