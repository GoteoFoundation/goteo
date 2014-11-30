<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Impersonate;

class ImpersonateTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Impersonate();

        $this->assertInstanceOf('\Goteo\Controller\Impersonate', $controller);

        return $controller;
    }
}
