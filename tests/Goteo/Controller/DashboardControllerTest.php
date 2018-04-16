<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\DashboardController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class DashboardControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        // This controller is for logged users only
        try {
            $controller = new DashboardController();
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\ControllerAccessDeniedException', $e);
        }

        Session::setUser(new User());
        $controller = new DashboardController();
        $this->assertInstanceOf('\Goteo\Controller\DashboardController', $controller);

        return $controller;
    }
}
