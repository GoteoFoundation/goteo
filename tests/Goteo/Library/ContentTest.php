<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Content;

class ContentTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Content();

        $this->assertInstanceOf('\Goteo\Library\Content', $converter);

        return $converter;
    }
}
