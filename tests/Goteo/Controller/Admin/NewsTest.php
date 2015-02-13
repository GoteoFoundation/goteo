<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\News;

class NewsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new News();

        $this->assertInstanceOf('\Goteo\Controller\Admin\News', $controller);

        return $controller;
    }
}
