<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\FooterSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class FooterSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new FooterSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\FooterSubController', $controller);

        return $controller;
    }
}
