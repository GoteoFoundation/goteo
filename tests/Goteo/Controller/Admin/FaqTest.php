<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Faq;

class FaqTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Faq();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Faq', $controller);

        return $controller;
    }
}
