<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\MailingSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class MailingSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new MailingSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\MailingSubController', $controller);

        return $controller;
    }
}
