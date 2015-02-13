<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Newsletter;

class NewsTestletter extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Newsletter();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Newsletter', $controller);

        return $controller;
    }
}
