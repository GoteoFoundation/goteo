<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\FaqSubController;

class FaqSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new FaqSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\FaqSubController', $controller);

        return $controller;
    }
}
