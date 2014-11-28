<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Newsletter;

class NewsletterTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Newsletter();

        $this->assertInstanceOf('\Goteo\Controller\Newsletter', $controller);

        return $controller;
    }
}
