<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Bazar;

class BazarTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Bazar();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Bazar', $controller);

        return $controller;
    }
}
