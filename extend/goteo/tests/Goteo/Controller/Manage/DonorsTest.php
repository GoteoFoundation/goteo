<?php


namespace Goteo\Controller\Manage\Tests;

use Goteo\Controller\Manage\Donors;

class DonorsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Donors();

        $this->assertInstanceOf('\Goteo\Controller\Manage\Donors', $controller);

        return $controller;
    }
}
