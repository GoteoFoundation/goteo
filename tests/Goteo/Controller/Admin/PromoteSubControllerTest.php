<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PromoteSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class PromoteSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new PromoteSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\PromoteSubController', $controller);

        return $controller;
    }
}
