<?php


namespace Goteo\Controller\Translate\Tests;

use Goteo\Controller\Translate\Texts;

class TextsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Texts();

        $this->assertInstanceOf('\Goteo\Controller\Translate\Texts', $controller);

        return $controller;
    }
}
