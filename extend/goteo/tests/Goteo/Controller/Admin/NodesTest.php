<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Nodes;

class NodesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Nodes();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Nodes', $controller);

        return $controller;
    }
}
