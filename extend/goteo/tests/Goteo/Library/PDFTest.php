<?php

namespace Goteo\Tests;


use Goteo\Library\PDF;


class PDFTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {

        $test = new PDF();
        $this->assertInstanceOf('\Goteo\Library\PDF', $test);

    }

    public function testDonativeCert() {
        $test2 = PDF::donativeCert();
        $this->assertInstanceOf('\Goteo\Library\PDF', $test2);
    }
}
