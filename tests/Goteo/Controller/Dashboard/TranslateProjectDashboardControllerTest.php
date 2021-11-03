<?php


namespace Goteo\Controller\Dashboard\Tests;

use Exception;
use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\Dashboard\TranslateProjectDashboardController;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use PHPUnit\Framework\TestCase;

class TranslateProjectDashboardControllerTest extends TestCase {

    public function testInstance(): TranslateProjectDashboardController
    {
        // This controller is for logged users only
        try {
            new TranslateProjectDashboardController();
        } catch(Exception $e) {
            $this->assertInstanceOf(ControllerAccessDeniedException::class, $e);
        }

        Session::setUser(new User());
        $controller = new TranslateProjectDashboardController();
        $this->assertInstanceOf(TranslateProjectDashboardController::class, $controller);

        return $controller;
    }
}
