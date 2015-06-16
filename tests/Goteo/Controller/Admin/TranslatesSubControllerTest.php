<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TranslatesSubController;

class TranslatesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TranslatesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\TranslatesSubController', $controller);

        return $controller;
    }
}
