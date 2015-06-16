<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\CampaignsSubController;

class CampaignsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new CampaignsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\CampaignsSubController', $controller);

        return $controller;
    }
}
