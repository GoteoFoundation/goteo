<?php


namespace Goteo\Controller\Manage\Tests;

use Goteo\Controller\Manage\Projects;

class ProjectsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Projects();

        $this->assertInstanceOf('\Goteo\Controller\Manage\Projects', $controller);

        return $controller;
    }
}
