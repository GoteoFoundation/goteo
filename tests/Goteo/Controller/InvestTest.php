<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Invest;

class InvestTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Invest();

        $this->assertInstanceOf('\Goteo\Controller\Invest', $controller);

        return $controller;
    }
}
