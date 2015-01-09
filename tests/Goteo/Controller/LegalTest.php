<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Legal;

class LegalTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Legal();

        $this->assertInstanceOf('\Goteo\Controller\Legal', $controller);

        return $controller;
    }
}
