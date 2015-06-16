<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\RewardsSubController;

class RewardsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new RewardsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\RewardsSubController', $controller);

        return $controller;
    }
}
