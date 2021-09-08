<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\NewsletterController;

class NewsletterControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new NewsletterController();

        $this->assertInstanceOf('\Goteo\Controller\NewsletterController', $controller);

        return $controller;
    }
}
