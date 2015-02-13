<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Sacaexcel;

class SacaexcelTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Sacaexcel();

        $this->assertInstanceOf('\Goteo\Controller\Sacaexcel', $controller);

        return $controller;
    }
}
