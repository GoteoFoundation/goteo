<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Contact;

class ContactTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Contact();

        $this->assertInstanceOf('\Goteo\Controller\Contact', $controller);

        return $controller;
    }
}
