<?php

namespace Goteo\Core\Tests;

class TestResource implements \Goteo\Core\Resource {
    public function __toString(){
        return 'ok';
    }
}
class TestResourceMIME implements \Goteo\Core\Resource\MIME {
    public function getMIME(){
        return 'text/plain';
    }
}

class ResourceTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {
        $test = new TestResource();
        $this->assertInstanceOf('\Goteo\Core\Resource', $test);
        $test = new TestResourceMIME();
        $this->assertInstanceOf('\Goteo\Core\Resource\MIME', $test);

    }
}
