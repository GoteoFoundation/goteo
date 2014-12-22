<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Texts;

class TextsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Texts();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Texts', $controller);

        return $controller;
    }
}
