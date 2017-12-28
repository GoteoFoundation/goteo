<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\Dashboard\ProjectDashboardController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class ProjectDashboardControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        // This controller is for logged users only
        try {
            $controller = new ProjectDashboardController();
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\ControllerAccessDeniedException', $e);
        }

        Session::setUser(new User());
        $controller = new ProjectDashboardController();
        $this->assertInstanceOf('\Goteo\Controller\Dashboard\ProjectDashboardController', $controller);

        return $controller;
    }
}
