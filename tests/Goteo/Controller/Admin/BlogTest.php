<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Blog;

class BlogTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Blog();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Blog', $controller);

        return $controller;
    }
}
