<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\ReviewsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class ReviewsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new ReviewsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\ReviewsSubController', $controller);

        return $controller;
    }
}
