<?php


namespace Goteo\Controller\Translate\Tests;

use Goteo\Controller\Translate\Pages;

class PagesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Pages();

        $this->assertInstanceOf('\Goteo\Controller\Translate\Pages', $controller);

        return $controller;
    }
}
