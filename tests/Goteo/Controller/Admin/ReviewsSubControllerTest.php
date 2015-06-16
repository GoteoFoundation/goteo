<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\ReviewsSubController;

class ReviewsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new ReviewsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\ReviewsSubController', $controller);

        return $controller;
    }
}
