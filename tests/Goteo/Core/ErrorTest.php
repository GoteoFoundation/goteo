<?php

namespace Goteo\Core\Tests;

use Goteo\Core\Error;

class ErrorTest extends \PHPUnit\Framework\TestCase {
    public function testInstance() {
        $err = new Error();
        $this->assertInstanceOf('\Goteo\Core\Error', $err);
        $this->assertInstanceOf('\Exception', $err);
    }
}
