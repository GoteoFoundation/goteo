<?php


namespace Goteo\Model\Call\Tests;

use Goteo\Model\Call\Banner;

class BannerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Banner();

        $this->assertInstanceOf('\Goteo\Model\Call\Banner', $converter);

        return $converter;
    }
}
