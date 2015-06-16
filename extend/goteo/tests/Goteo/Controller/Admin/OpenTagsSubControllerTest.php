<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\OpenTagsSubController;

class OpenTagsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new OpenTagsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\OpenTagsSubController', $controller);

        return $controller;
    }
}
