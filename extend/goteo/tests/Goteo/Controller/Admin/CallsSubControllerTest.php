<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CallsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class CallsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new CallsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\CallsSubController', $controller);

        return $controller;
    }
}
