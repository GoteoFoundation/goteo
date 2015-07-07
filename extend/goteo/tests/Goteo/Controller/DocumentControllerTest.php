<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\DocumentController;

class DocumentControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new DocumentController();

        $this->assertInstanceOf('\Goteo\Controller\DocumentController', $controller);

        return $controller;
    }
}
