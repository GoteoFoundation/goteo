<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Image;

class ImageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Image();

        $this->assertInstanceOf('\Goteo\Model\Project\Image', $converter);

        return $converter;
    }
}
