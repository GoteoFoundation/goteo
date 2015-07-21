<?php


namespace Goteo\Command\Tests;

use Goteo\Command\CallsWatcher;

class CallsWatcherTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new CallsWatcher();

        $this->assertInstanceOf('\Goteo\Command\CallsWatcher', $controller);

        return $controller;
    }
}
