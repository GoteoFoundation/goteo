<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Feed;

class FeedTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Feed();

        $this->assertInstanceOf('\Goteo\Library\Feed', $converter);

        return $converter;
    }
}
