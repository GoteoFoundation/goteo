<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\ProjectsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class ProjectsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');
        $controller = new ProjectsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\ProjectsSubController', $controller);

        return $controller;
    }
}
