<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Review;

class ReviewTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Review();

        $this->assertInstanceOf('\Goteo\Controller\Review', $controller);

        return $controller;
    }
}
