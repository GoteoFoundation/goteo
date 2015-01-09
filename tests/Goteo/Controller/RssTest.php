<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Rss;

class RssTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Rss();

        $this->assertInstanceOf('\Goteo\Controller\Rss', $controller);

        return $controller;
    }
}
