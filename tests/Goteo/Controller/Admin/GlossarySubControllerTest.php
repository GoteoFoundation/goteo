<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\GlossarySubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class GlossarySubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new GlossarySubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\GlossarySubController', $controller);

        return $controller;
    }
}
