<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Calendar;

class CalendarTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Calendar();

        $this->assertInstanceOf('\Goteo\Controller\Calendar', $controller);

        return $controller;
    }
}
