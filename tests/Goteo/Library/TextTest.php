<?php


namespace Goteo\Tests;

use Goteo\Library\Text;

class TextTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Text();

        $this->assertInstanceOf('\Goteo\Library\Text', $converter);

        return $converter;
    }
}
