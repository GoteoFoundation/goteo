<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\InvestController;
use PHPUnit\Framework\TestCase;

class InvestControllerTest extends TestCase {

    public function testInstance() {

        $controller = new InvestController();

        $this->assertInstanceOf('\Goteo\Controller\InvestController', $controller);

        return $controller;
    }
}
