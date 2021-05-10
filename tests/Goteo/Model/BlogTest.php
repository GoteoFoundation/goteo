<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Blog;

class BlogTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Blog();

        $this->assertInstanceOf('\Goteo\Model\Blog', $converter);

        return $converter;
    }
}
