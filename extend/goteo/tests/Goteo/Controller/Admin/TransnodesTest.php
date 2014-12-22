<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Transnodes;

class TransnodesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Transnodes();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Transnodes', $controller);

        return $controller;
    }
}
