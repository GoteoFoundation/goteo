<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\LicensesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class LicensesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new LicensesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\LicensesSubController', $controller);

        return $controller;
    }
}
