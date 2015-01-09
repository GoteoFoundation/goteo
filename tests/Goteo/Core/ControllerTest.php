<?php

namespace Goteo\Core\Tests;

use Goteo\Core\Controller;

class TestController extends Controller {
}

class ControllerTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {

        $test = new TestController();
        $this->assertInstanceOf('\Goteo\Core\Controller', $test);
    }
}
