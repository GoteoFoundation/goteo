<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Info;

class InfoTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Info();

        $this->assertInstanceOf('\Goteo\Model\Info', $converter);

        return $converter;
    }
}
