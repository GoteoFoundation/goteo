<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Stories;

class StoriesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Stories();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Stories', $controller);

        return $controller;
    }
}
