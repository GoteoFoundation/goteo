<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\News;

class NewsTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new News();

        $this->assertInstanceOf('\Goteo\Controller\News', $controller);

        return $controller;
    }
}
