<?php


namespace Goteo\Tests;

use Goteo\Library\Feed;

class FeedTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Feed();

        $this->assertInstanceOf('\Goteo\Library\Feed', $converter);

        return $converter;
    }
}
