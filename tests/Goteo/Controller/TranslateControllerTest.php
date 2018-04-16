<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\TranslateController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class TranslateControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        // This controller is for logged users only
        try {
            $controller = new TranslateController();
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\ControllerAccessDeniedException', $e);
        }

        $user = new User();
        $user->roles['admin'] = 1;
        Session::setUser($user);
        $controller = new TranslateController();
        $this->assertInstanceOf('\Goteo\Controller\TranslateController', $controller);

        return $controller;
    }
}
