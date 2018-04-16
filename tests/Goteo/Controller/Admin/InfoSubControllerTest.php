<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\InfoSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class InfoSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new InfoSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\InfoSubController', $controller);

        return $controller;
    }
}
