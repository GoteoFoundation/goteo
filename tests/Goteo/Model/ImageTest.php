<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Image;

class ImageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Image();

        $this->assertInstanceOf('\Goteo\Model\Image', $converter);

        return $converter;
    }
}
