<?php


namespace Goteo\Library\Tests;

use Goteo\Library\AmazonSns;

class AmazonSnsTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new AmazonSns();

        $this->assertInstanceOf('\Goteo\Library\AmazonSns', $converter);

        return $converter;
    }
}
