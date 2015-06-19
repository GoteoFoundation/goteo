<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\TranscallsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class TranscallsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new TranscallsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\TranscallsSubController', $controller);

        return $controller;
    }
}
