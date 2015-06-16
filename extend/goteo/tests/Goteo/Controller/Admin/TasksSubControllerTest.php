<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TasksSubController;

class TasksSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TasksSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\TasksSubController', $controller);

        return $controller;
    }
}
