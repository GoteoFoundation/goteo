<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Projects;

class ProjectsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Projects();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Projects', $controller);

        return $controller;
    }
}
