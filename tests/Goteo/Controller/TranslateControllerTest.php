<?php

namespace Tests\Goteo\Controller;

use Exception;
use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Controller\TranslateController;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class TranslateControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance(): TranslateController
    {
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
