<?php


namespace Goteo\Controller\Translate\Tests;

use Goteo\Controller\Translate\Node;

class NodeTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Node();

        $this->assertInstanceOf('\Goteo\Controller\Translate\Node', $controller);

        return $controller;
    }
}
