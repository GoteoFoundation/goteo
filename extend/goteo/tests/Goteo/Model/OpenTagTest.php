<?php


namespace Goteo\Model\Tests;

use Goteo\Model\OpenTag;

class OpenTagTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new OpenTag();

        $this->assertInstanceOf('\Goteo\Model\OpenTag', $converter);

        return $converter;
    }
}
