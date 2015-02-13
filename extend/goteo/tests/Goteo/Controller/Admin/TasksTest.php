<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Tasks;

class TasksTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Tasks();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Tasks', $controller);

        return $controller;
    }
}
