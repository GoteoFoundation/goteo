<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\WordcountSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class WordcountSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create('/admin');

        $controller = new WordcountSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\WordcountSubController', $controller);

        return $controller;
    }
}
