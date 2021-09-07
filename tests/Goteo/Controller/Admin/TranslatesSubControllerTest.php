<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TranslatesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class TranslatesSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new TranslatesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\TranslatesSubController', $controller);

        return $controller;
    }
}
