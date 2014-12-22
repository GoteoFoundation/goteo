<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Reviews;

class ReviewsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Reviews();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Reviews', $controller);

        return $controller;
    }
}
