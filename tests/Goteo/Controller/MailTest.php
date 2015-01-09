<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Mail;

class MailTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Mail();

        $this->assertInstanceOf('\Goteo\Controller\Mail', $controller);

        return $controller;
    }
}
