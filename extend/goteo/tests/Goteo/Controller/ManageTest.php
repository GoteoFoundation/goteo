<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Manage;

class ManageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Manage();

        $this->assertInstanceOf('\Goteo\Controller\Manage', $controller);

        return $controller;
    }
}
