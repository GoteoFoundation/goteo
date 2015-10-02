<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Translate;

class TranslateTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Translate();

        $this->assertInstanceOf('\Goteo\Controller\Translate', $controller);

        return $controller;
    }
}
