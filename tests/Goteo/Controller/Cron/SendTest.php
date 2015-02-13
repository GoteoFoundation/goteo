<?php


namespace Goteo\Controller\Cron\Tests;

use Goteo\Controller\Cron\Send;

class SendTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Send();

        $this->assertInstanceOf('\Goteo\Controller\Cron\Send', $controller);

        return $controller;
    }
}
