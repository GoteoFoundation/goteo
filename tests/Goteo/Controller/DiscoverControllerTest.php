<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\DiscoverController;

class DiscoverControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new DiscoverController();

        $this->assertInstanceOf('\Goteo\Controller\DiscoverController', $controller);

        return $controller;
    }
}
