<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Bazaar;

class BazaarTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Bazaar();

        $this->assertInstanceOf('\Goteo\Controller\Bazaar', $controller);

        return $controller;
    }
}
