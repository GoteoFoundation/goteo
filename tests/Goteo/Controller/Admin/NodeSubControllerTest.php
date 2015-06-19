<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NodeSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class NodeSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new NodeSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\NodeSubController', $controller);

        return $controller;
    }
}
