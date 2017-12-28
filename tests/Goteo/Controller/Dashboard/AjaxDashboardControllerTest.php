<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\Dashboard\AjaxDashboardController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class AjaxDashboardControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        // This controller is for logged users only
        try {
            $controller = new AjaxDashboardController();
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\ControllerAccessDeniedException', $e);
        }

        Session::setUser(new User());
        $controller = new AjaxDashboardController();
        $this->assertInstanceOf('\Goteo\Controller\Dashboard\AjaxDashboardController', $controller);

        return $controller;
    }
}
