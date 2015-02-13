<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Sended;

class SendedTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Sended();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Sended', $controller);

        return $controller;
    }
}
