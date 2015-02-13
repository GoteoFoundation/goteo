<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Controller\Dashboard\Translates;

class TranslatesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Translates();

        $this->assertInstanceOf('\Goteo\Controller\Dashboard\Translates', $controller);

        return $controller;
    }
}
