<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Cost;

class CostTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Cost();

        $this->assertInstanceOf('\Goteo\Model\Project\Cost', $converter);

        return $converter;
    }
}
