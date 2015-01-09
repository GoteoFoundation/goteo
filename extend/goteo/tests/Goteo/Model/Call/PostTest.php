<?php


namespace Goteo\Model\Call\Tests;

use Goteo\Model\Call\Post;

class PostTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Post();

        $this->assertInstanceOf('\Goteo\Model\Call\Post', $converter);

        return $converter;
    }
}
