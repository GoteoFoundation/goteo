<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Promote;

class PromoteTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Promote();

        $this->assertInstanceOf('\Goteo\Model\Promote', $converter);

        return $converter;
    }
}
