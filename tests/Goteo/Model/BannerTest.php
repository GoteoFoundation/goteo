<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Banner;

class BannerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Banner();

        $this->assertInstanceOf('\Goteo\Model\Banner', $converter);

        return $converter;
    }
}
