<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TextsSubController;

class TextsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TextsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\TextsSubController', $controller);

        return $controller;
    }
}
