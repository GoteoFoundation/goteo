<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Blog;

class BlogTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Blog();

        $this->assertInstanceOf('\Goteo\Controller\Blog', $controller);

        return $controller;
    }
}
