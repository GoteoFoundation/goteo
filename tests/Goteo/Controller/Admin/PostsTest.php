<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Posts;

class PostsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Posts();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Posts', $controller);

        return $controller;
    }
}
