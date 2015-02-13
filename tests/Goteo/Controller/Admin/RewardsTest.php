<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Rewards;

class RewardsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Rewards();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Rewards', $controller);

        return $controller;
    }
}
