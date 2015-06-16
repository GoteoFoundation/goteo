<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\LicensesSubController;

class LicensesSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new LicensesSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\LicensesSubController', $controller);

        return $controller;
    }
}
