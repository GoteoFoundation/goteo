<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\InvestsSubController;

class InvestsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new InvestsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\InvestsSubController', $controller);

        return $controller;
    }
}
