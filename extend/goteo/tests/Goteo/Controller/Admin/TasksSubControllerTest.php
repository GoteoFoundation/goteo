<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TasksSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class TasksSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new TasksSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\TasksSubController', $controller);

        return $controller;
    }
}
