<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Transcalls;

class TranscallsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Transcalls();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Transcalls', $controller);

        return $controller;
    }
}
