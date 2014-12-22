<?php


namespace Goteo\Model\Blog\Post\Tests;

use Goteo\Model\Blog\Post\Tag;

class TagTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Tag();

        $this->assertInstanceOf('\Goteo\Model\Blog\Post\Tag', $converter);

        return $converter;
    }
}
