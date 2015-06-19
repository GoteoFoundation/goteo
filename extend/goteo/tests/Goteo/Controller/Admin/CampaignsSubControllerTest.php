<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CampaignsSubController;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class CampaignsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        $user = new User();
        $node = 'test';
        $request = Request::create();

        $controller = new CampaignsSubController($node, $user, $request);

        $this->assertInstanceOf('\Goteo\Controller\Admin\CampaignsSubController', $controller);

        return $controller;
    }
}
