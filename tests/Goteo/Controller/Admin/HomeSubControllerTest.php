<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\HomeSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class HomeSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new HomeSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\HomeSubController', $controller);

        return $controller;
    }
}
