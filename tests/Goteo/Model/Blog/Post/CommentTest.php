<?php


namespace Goteo\Model\Blog\Post\Tests;

use Goteo\Model\Blog\Post\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Comment();

        $this->assertInstanceOf('\Goteo\Model\Blog\Post\Comment', $converter);

        return $converter;
    }
}
