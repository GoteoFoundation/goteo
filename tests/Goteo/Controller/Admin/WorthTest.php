<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Worth;

class WorthTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Worth();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Worth', $controller);

        return $controller;
    }
}
