<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\FaqSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class FaqSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new FaqSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\FaqSubController', $controller);

        return $controller;
    }
}
