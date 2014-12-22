<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Icon;

class IconTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Icon();

        $this->assertInstanceOf('\Goteo\Model\Icon', $converter);

        return $converter;
    }
}
