<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Templates;

class TemplatesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Templates();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Templates', $controller);

        return $controller;
    }
}
