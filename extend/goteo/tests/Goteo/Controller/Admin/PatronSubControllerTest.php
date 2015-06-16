<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\PatronSubController;

class PatronSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new PatronSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\PatronSubController', $controller);

        return $controller;
    }
}
