<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CriteriaSubController;

class CriteriaSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new CriteriaSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\CriteriaSubController', $controller);

        return $controller;
    }
}
