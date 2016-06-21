<?php


namespace Goteo\Model\Call\Tests;

use Goteo\Model\Call\Category;

class CategoryTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Category();

        $this->assertInstanceOf('\Goteo\Model\Call\Category', $converter);

        return $converter;
    }
}
