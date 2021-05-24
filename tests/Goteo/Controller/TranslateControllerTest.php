<?php


namespace Goteo\Controller\Dashboard\Tests;

use Exception;
use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\TranslateController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class TranslateControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance(): TranslateController
    {
        // This controller is for logged users only
        try {
            new TranslateController();
        } catch(Exception $e) {
            $this->assertInstanceOf(ControllerAccessDeniedException::class, $e);
        }

        $user = new User();
        $user->roles['admin'] = 1;
        Session::setUser($user);
        $controller = new TranslateController();
        $this->assertInstanceOf('\Goteo\Controller\TranslateController', $controller);

        return $controller;
    }
}
