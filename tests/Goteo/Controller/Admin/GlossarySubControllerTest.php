<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\GlossarySubController;

class GlossarySubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new GlossarySubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\GlossarySubController', $controller);

        return $controller;
    }
}
