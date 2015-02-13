<?php


namespace Goteo\Controller\Translate\Tests;

use Goteo\Controller\Translate\Tables;

class TablesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Tables();

        $this->assertInstanceOf('\Goteo\Controller\Translate\Tables', $controller);

        return $controller;
    }
}
