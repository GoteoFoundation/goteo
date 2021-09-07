<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\RecentSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class RecentSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new RecentSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\RecentSubController', $controller);

        return $controller;
    }
}
