<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Campaigns;

class CampaignsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Campaigns();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Campaigns', $controller);

        return $controller;
    }
}
