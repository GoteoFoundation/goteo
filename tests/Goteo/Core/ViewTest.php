<?php

namespace Goteo\Tests;

use \Goteo\Core\View,
    \Goteo\Core\View\Exception;

class ViewTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {
        $test = new View(__FILE__);
        $this->assertInstanceOf('\Goteo\Core\View', $test);
        $test = new Exception();
        $this->assertInstanceOf('\Goteo\Core\View\Exception', $test);

    }
}
