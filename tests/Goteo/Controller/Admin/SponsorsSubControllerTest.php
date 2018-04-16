<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\SponsorsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class SponsorsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new SponsorsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\SponsorsSubController', $controller);

        return $controller;
    }
}
