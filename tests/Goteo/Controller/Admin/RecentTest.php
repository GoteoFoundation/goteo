<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Recent;

class RecentTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Recent();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Recent', $controller);

        return $controller;
    }
}
