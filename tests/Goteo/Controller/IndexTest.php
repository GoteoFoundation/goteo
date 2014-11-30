<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Index;

class IndexTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Index();

        $this->assertInstanceOf('\Goteo\Controller\Index', $controller);

        return $controller;
    }
}
