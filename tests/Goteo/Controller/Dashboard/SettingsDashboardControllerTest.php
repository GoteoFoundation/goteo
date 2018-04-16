<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\Dashboard\SettingsDashboardController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class SettingsDashboardControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        // This controller is for logged users only
        try {
            $controller = new SettingsDashboardController();
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\ControllerAccessDeniedException', $e);
        }

        Session::setUser(new User());
        $controller = new SettingsDashboardController();
        $this->assertInstanceOf('\Goteo\Controller\Dashboard\SettingsDashboardController', $controller);

        return $controller;
    }
}
