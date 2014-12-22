<?php


namespace Goteo\Model\Call\Tests;

use Goteo\Model\Call\Icon;

class IconTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Icon();

        $this->assertInstanceOf('\Goteo\Model\Call\Icon', $converter);

        return $converter;
    }
}
