<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\ProjectController;

class ProjectControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new ProjectController();

        $this->assertInstanceOf('\Goteo\Controller\ProjectController', $controller);

        return $controller;
    }
}
