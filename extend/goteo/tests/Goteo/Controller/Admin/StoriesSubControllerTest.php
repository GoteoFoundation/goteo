<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\StoriesSubController;

class StoriesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new StoriesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\StoriesSubController', $controller);

        return $controller;
    }
}
