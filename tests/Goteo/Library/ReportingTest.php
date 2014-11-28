<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Reporting;

class ReportingTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Reporting();

        $this->assertInstanceOf('\Goteo\Library\Reporting', $converter);

        return $converter;
    }
}
