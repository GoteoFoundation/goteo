<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CriteriaSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class CriteriaSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new CriteriaSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\CriteriaSubController', $controller);

        return $controller;
    }
}
