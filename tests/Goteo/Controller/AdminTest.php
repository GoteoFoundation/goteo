<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Admin;

class AdminTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Admin();

        $this->assertInstanceOf('\Goteo\Controller\Admin', $controller);

        return $controller;
    }
}
