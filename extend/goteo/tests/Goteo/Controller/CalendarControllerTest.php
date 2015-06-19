<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\CalendarController;

class CalendarControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new CalendarController();

        $this->assertInstanceOf('\Goteo\Controller\CalendarController', $controller);

        return $controller;
    }
}
