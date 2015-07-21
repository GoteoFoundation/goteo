<?php


namespace Goteo\Command\Tests;

use Goteo\Command\DbVerifier;

class DbVerifierTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new DbVerifier();

        $this->assertInstanceOf('\Goteo\Command\DbVerifier', $controller);

        return $controller;
    }
}
