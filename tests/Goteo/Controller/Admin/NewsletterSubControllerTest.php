<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NewsletterSubController;

class NewsletterSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new NewsletterSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\NewsletterSubController', $controller);

        return $controller;
    }
}
