<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\StoriesSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class StoriesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new StoriesSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\StoriesSubController', $controller);

        return $controller;
    }
}
