<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\About;

class AboutTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new About();

        $this->assertInstanceOf('\Goteo\Controller\About', $controller);

        return $controller;
    }
}
