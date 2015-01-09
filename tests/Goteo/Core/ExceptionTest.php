<?php

namespace Goteo\Core\Tests;


class TestException extends \Goteo\Core\Exception {
}

class ExceptionTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {

        $test = new TestException();
        $this->assertInstanceOf('\Goteo\Core\Exception', $test);
    }
}
