<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Sponsors;

class SponsorsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Sponsors();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Sponsors', $controller);

        return $controller;
    }
}
