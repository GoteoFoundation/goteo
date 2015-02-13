<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Service();

        $this->assertInstanceOf('\Goteo\Controller\Service', $controller);

        return $controller;
    }
}
