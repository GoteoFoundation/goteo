<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Banners;

class BannersTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Banners();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Banners', $controller);

        return $controller;
    }
}
