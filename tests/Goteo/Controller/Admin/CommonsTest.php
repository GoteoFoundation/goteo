<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Commons;

class CommonsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Commons();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Commons', $controller);

        return $controller;
    }
}
