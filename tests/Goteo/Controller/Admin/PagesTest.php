<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Pages;

class PagesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Pages();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Pages', $controller);

        return $controller;
    }
}
