<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Licenses;

class LicensesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Licenses();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Licenses', $controller);

        return $controller;
    }
}
