<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Media;

class MediaTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Media('http://goteo.org');

        $this->assertInstanceOf('\Goteo\Model\Project\Media', $converter);

        return $converter;
    }
}
