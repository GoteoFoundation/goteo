<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Tpv;

class TpvTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Tpv();

        $this->assertInstanceOf('\Goteo\Controller\Tpv', $controller);

        return $controller;
    }
}
