<?php


namespace Goteo\Model\Tests;

use Goteo\Model\News;

class NewsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new News();

        $this->assertInstanceOf('\Goteo\Model\News', $converter);

        return $converter;
    }
}
