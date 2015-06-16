<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TagsSubController;

class TagsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TagsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\TagsSubController', $controller);

        return $controller;
    }
}
