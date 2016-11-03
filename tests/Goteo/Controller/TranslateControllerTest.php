<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\TranslateController;

class TranslateControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TranslateController();

        $this->assertInstanceOf('\Goteo\Controller\TranslateController', $controller);

        return $controller;
    }
}
