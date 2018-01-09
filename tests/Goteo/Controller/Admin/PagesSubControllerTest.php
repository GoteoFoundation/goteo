<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PagesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class PagesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new PagesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\PagesSubController', $controller);

        return $controller;
    }
}
