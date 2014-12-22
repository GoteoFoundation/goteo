<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Controller\Dashboard\Projects;

class ProjectsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Projects();

        $this->assertInstanceOf('\Goteo\Controller\Dashboard\Projects', $controller);

        return $controller;
    }
}
