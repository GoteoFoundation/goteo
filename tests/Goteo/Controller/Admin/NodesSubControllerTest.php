<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NodesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class NodesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new NodesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\NodesSubController', $controller);

        return $controller;
    }
}
