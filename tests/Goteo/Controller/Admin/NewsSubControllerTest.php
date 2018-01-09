<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NewsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class NewsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new NewsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\NewsSubController', $controller);

        return $controller;
    }
}
