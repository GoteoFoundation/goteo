<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Node;

class NodeTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Node();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Node', $controller);

        return $controller;
    }
}
