<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Footer;

class FooterTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Footer();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Footer', $controller);

        return $controller;
    }
}
