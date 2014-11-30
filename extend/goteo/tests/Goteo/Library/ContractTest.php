<?php

namespace Goteo\Tests;


use Goteo\Library\PDFContract;


class PDFContractTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {

        $test = new PDFContract();
        $this->assertInstanceOf('\Goteo\Library\PDFContract', $test);

    }
}
