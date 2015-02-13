<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Reports;

class ReportsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Reports();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Reports', $controller);

        return $controller;
    }
}
