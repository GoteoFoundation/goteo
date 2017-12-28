<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\SentSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class SentSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new SentSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\SentSubController', $controller);

        return $controller;
    }
}
