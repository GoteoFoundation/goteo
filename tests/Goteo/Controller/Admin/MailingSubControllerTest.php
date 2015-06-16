<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\MailingSubController;

class MailingSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new MailingSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\MailingSubController', $controller);

        return $controller;
    }
}
