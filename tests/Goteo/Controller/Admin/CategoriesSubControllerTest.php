<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CategoriesSubController;

class CategoriesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new CategoriesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\CategoriesSubController', $controller);

        return $controller;
    }
}
