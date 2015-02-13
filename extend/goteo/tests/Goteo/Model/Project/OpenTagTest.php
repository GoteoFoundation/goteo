<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\OpenTag;

class OpenTagTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new OpenTag();

        $this->assertInstanceOf('\Goteo\Model\Project\OpenTag', $converter);

        return $converter;
    }
}
