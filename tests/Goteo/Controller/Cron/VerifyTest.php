<?php


namespace Goteo\Controller\Cron\Tests;

use Goteo\Controller\Cron\Verify;

class VerifyTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Verify();

        $this->assertInstanceOf('\Goteo\Controller\Cron\Verify', $controller);

        return $controller;
    }
}
