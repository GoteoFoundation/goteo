<?php


namespace Goteo\Controller\Cron\Tests;

use Goteo\Controller\Cron\Execute;

class ExecuteTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Execute();

        $this->assertInstanceOf('\Goteo\Controller\Cron\Execute', $controller);

        return $controller;
    }
}
