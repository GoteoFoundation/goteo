<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PatronSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class PatronSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new PatronSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\PatronSubController', $controller);

        return $controller;
    }
}
