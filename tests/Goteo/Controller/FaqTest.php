<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Faq;

class FaqTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Faq();

        $this->assertInstanceOf('\Goteo\Controller\Faq', $controller);

        return $controller;
    }
}
