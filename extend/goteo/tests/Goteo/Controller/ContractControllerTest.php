<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\ContractController;

class ContractControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new ContractController();

        $this->assertInstanceOf('\Goteo\Controller\ContractController', $controller);

        return $controller;
    }
}
