<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Post;

class PostTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Post();

        $this->assertInstanceOf('\Goteo\Model\Post', $converter);

        return $converter;
    }
}
