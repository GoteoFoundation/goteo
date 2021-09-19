<?php

namespace Goteo\Core\Tests;

use Goteo\Core\Controller;

class TestController extends Controller {
}

class ControllerTest extends \PHPUnit\Framework\TestCase {
    public function testInstance() {

        $test = new TestController();
        $this->assertInstanceOf('\Goteo\Core\Controller', $test);
    }
}
