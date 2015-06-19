<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\ReportsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class ReportsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new ReportsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\ReportsSubController', $controller);

        return $controller;
    }
}
