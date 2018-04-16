<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CategoriesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class CategoriesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new CategoriesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\CategoriesSubController', $controller);

        return $controller;
    }
}
