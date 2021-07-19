<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\AccountsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class AccountsSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new AccountsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\AccountsSubController', $controller);

        return $controller;
    }
}
