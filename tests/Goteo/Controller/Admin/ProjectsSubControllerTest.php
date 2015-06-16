<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\ProjectsSubController;

class ProjectsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new ProjectsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\ProjectsSubController', $controller);

        return $controller;
    }
}
