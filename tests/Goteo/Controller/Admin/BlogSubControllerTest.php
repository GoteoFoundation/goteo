<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\BlogSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class BlogSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new BlogSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\BlogSubController', $controller);

        return $controller;
    }
}
