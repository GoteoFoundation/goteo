<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Info;

class InfoTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Info();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Info', $controller);

        return $controller;
    }
}
