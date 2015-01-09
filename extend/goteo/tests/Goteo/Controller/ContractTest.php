<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Contract;

class ContractTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Contract();

        $this->assertInstanceOf('\Goteo\Controller\Contract', $controller);

        return $controller;
    }
}
