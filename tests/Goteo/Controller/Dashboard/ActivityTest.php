<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Controller\Dashboard\Activity;

class ActivityTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Activity();

        $this->assertInstanceOf('\Goteo\Controller\Dashboard\Activity', $controller);

        return $controller;
    }
}
