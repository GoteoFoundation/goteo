<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\FaqController;
use Goteo\TestCase;

class FaqControllerTest extends TestCase {

    public function testInstance(): FaqController
    {

        $controller = new FaqController();

        $this->assertInstanceOf(FaqController::class, $controller);

        return $controller;
    }
}
