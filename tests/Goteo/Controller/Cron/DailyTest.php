<?php


namespace Goteo\Controller\Cron\Tests;

use Goteo\Controller\Cron\Daily;

class DailyTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Daily();

        $this->assertInstanceOf('\Goteo\Controller\Cron\Daily', $controller);

        return $controller;
    }
}
