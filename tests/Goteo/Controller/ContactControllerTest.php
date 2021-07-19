<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\ContactController;

class ContactControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new ContactController();

        $this->assertInstanceOf('\Goteo\Controller\ContactController', $controller);

        return $controller;
    }
}
