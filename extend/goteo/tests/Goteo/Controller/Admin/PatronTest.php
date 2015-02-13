<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Patron;

class PatronTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Patron();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Patron', $controller);

        return $controller;
    }
}
