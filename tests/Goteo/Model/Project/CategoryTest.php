<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Category;

class CategoryTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Category();

        $this->assertInstanceOf('\Goteo\Model\Project\Category', $converter);

        return $converter;
    }
}
