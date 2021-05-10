<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\RssController;

class RssControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new RssController();

        $this->assertInstanceOf('\Goteo\Controller\RssController', $controller);

        return $controller;
    }
}
