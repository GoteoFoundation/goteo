<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\IconsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class IconsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new IconsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\IconsSubController', $controller);

        return $controller;
    }
}
