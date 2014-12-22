<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Calls;

class CallsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Calls();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Calls', $controller);

        return $controller;
    }
}
