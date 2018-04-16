<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TransnodesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class TransnodesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new TransnodesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\TransnodesSubController', $controller);

        return $controller;
    }
}
