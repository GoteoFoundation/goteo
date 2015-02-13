<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Contract;

class ContractTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Contract();

        $this->assertInstanceOf('\Goteo\Model\Contract', $converter);

        return $converter;
    }
}
