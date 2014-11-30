<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Image;

class ImageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Image();

        $this->assertInstanceOf('\Goteo\Controller\Image', $controller);

        return $controller;
    }
}
