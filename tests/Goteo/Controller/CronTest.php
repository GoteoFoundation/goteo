<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Cron;

class CronTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Cron();

        $this->assertInstanceOf('\Goteo\Controller\Cron', $controller);

        return $controller;
    }
}
