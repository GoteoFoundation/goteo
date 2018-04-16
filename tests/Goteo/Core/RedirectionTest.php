<?php

namespace Goteo\Core\Tests;

use Goteo\Core\Redirection;

class RedirectionTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {
        $test = new Redirection('http://www.google.com');
        $this->assertInstanceOf('\Goteo\Core\Redirection', $test);
        $this->assertInstanceOf('\Exception', $test);
        return $test;
    }

    public function testUrl() {
        $url = 'http://www.google.com';
        $test = new Redirection($url);
        $this->assertEquals($url, $test->getURL());
    }
}
