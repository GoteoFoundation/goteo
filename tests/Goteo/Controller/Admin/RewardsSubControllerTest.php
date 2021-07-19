<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\RewardsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class RewardsSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new RewardsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\RewardsSubController', $controller);

        return $controller;
    }
}
