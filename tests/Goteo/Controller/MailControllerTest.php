<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\MailController;

class MailControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new MailController();

        $this->assertInstanceOf('\Goteo\Controller\MailController', $controller);

        return $controller;
    }
}
