<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Preview;

class PreviewTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Preview();

        $this->assertInstanceOf('\Goteo\Controller\Preview', $controller);

        return $controller;
    }
}
