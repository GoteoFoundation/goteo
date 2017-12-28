<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\NewsletterController;

class NewsletterControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new NewsletterController();

        $this->assertInstanceOf('\Goteo\Controller\NewsletterController', $controller);

        return $controller;
    }
}
