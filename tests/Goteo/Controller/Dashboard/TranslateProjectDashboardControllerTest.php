<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\Dashboard\TranslateProjectDashboardController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class TranslateProjectDashboardControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        // This controller is for logged users only
        try {
            $controller = new TranslateProjectDashboardController();
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\ControllerAccessDeniedException', $e);
        }

        Session::setUser(new User());
        $controller = new TranslateProjectDashboardController();
        $this->assertInstanceOf('\Goteo\Controller\Dashboard\TranslateProjectDashboardController', $controller);

        return $controller;
    }
}
