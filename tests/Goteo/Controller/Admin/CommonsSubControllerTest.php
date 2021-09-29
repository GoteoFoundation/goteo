<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CommonsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class CommonsSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new CommonsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\CommonsSubController', $controller);

        return $controller;
    }
}
