<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\InvestController;

class InvestControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new InvestController();

        $this->assertInstanceOf('\Goteo\Controller\InvestController', $controller);

        return $controller;
    }
}
