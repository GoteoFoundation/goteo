<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Translates;

class TranslatesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Translates();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Translates', $controller);

        return $controller;
    }
}
