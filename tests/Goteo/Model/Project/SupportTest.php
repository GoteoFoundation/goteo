<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Support;

class SupportTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Support();

        $this->assertInstanceOf('\Goteo\Model\Project\Support', $converter);

        return $converter;
    }
}
