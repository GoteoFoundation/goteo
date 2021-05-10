<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\GlossaryController;

class GlossaryControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new GlossaryController();

        $this->assertInstanceOf('\Goteo\Controller\GlossaryController', $controller);

        return $controller;
    }


}
