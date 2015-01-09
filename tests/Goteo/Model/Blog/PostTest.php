<?php


namespace Goteo\Model\Blog\Tests;

use Goteo\Model\Blog\Post;

class PostTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Post();

        $this->assertInstanceOf('\Goteo\Model\Blog\Post', $converter);

        return $converter;
    }
}
