<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Category;

class CategoryTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Category();

        $this->assertInstanceOf('\Goteo\Model\Category', $converter);

        return $converter;
    }
}
