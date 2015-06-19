<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\BazarSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class BazarSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new BazarSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\BazarSubController', $controller);

        return $controller;
    }
}
