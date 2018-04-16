<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\BannersSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class BannersSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new BannersSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\BannersSubController', $controller);

        return $controller;
    }
}
