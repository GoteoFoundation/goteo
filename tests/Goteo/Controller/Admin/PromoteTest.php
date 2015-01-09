<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Promote;

class PromoteTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Promote();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Promote', $controller);

        return $controller;
    }
}
