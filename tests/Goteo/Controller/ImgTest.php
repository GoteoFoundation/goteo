<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Img;

class ImgTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Img();

        $this->assertInstanceOf('\Goteo\Controller\Img', $controller);

        return $controller;
    }
}
