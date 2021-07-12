<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PostsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class PostsSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new PostsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\PostsSubController', $controller);

        return $controller;
    }
}
