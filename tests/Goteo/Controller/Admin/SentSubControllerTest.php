<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\SentSubController;

class SentSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new SentSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\SentSubController', $controller);

        return $controller;
    }
}
