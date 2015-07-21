<?php


namespace Goteo\Command\Tests;

use Goteo\Command\ProjectsWatcher;

class ProjectsWatcherTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new ProjectsWatcher();

        $this->assertInstanceOf('\Goteo\Command\ProjectsWatcher', $controller);

        return $controller;
    }
}
