<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\WordcountSubController;

class WordcountSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new WordcountSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\WordcountSubController', $controller);

        return $controller;
    }
}
