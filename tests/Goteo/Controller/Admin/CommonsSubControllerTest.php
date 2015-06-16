<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CommonsSubController;

class CommonsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new CommonsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\CommonsSubController', $controller);

        return $controller;
    }
}
