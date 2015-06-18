<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Node;

class NodeTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Node();

        $this->assertInstanceOf('\Goteo\Model\Node', $converter);

        return $converter;
    }
}
