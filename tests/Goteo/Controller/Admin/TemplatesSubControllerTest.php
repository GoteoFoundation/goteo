<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TemplatesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class TemplatesSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new TemplatesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\TemplatesSubController', $controller);

        return $controller;
    }
}
