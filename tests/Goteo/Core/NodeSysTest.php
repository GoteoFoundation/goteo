<?php

namespace Goteo\Tests;


use Goteo\Core\NodeSys;


class NodeSysTest extends \PHPUnit\Framework\TestCase {
    public function testInstance() {

        $test = new NodeSys();
        $this->assertInstanceOf('\Goteo\Core\NodeSys', $test);
    }
}
