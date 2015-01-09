<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Home;

class HomeTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Home();

        $this->assertInstanceOf('\Goteo\Model\Home', $converter);

        return $converter;
    }
}
