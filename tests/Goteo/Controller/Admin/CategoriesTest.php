<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Categories;

class CategoriesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Categories();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Categories', $controller);

        return $controller;
    }
}
