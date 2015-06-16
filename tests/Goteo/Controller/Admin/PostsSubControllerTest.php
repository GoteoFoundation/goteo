<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PostsSubController;

class PostsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new PostsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\PostsSubController', $controller);

        return $controller;
    }
}
