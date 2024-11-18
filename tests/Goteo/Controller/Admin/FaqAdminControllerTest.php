<?php

namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\FaqAdminController;
use Goteo\TestCase;

class FaqAdminControllerTest extends TestCase {

    public function testInstance(): FaqAdminController
    {
        $controller = new FaqAdminController();

        $this->assertInstanceOf(FaqAdminController::class, $controller);

        return $controller;
    }
}
