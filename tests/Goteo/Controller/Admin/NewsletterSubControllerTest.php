<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\NewsletterSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class NewsletterSubControllerTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new NewsletterSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\NewsletterSubController', $controller);

        return $controller;
    }
}
