<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Stories;

class StoriesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Stories();

        $this->assertInstanceOf('\Goteo\Model\Stories', $converter);

        return $converter;
    }
}
