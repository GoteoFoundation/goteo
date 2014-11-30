<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Project();

        $this->assertInstanceOf('\Goteo\Controller\Project', $controller);

        return $controller;
    }
}
