<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Blog;

class BlogTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Blog();

        $this->assertInstanceOf('\Goteo\Model\Blog', $converter);

        return $converter;
    }
}
