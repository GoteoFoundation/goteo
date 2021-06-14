<?php


namespace Goteo\Command\Tests;

use Goteo\Console\Command\ProjectWatcherCommand;

class ProjectWatcherCommandTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new ProjectWatcherCommand();

        $this->assertInstanceOf('\Goteo\Console\Command\ProjectWatcherCommand', $controller);

        return $controller;
    }
}
