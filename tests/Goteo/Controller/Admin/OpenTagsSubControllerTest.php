<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\OpenTagsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class OpenTagsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new OpenTagsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\OpenTagsSubController', $controller);

        return $controller;
    }
}
