<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\WorthSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class WorthSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new WorthSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\WorthSubController', $controller);

        return $controller;
    }
}
