<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TextsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class TextsSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new TextsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\TextsSubController', $controller);

        return $controller;
    }
}
