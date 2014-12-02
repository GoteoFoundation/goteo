<?php

namespace Goteo\Tests;


use Goteo\Library\Tpv;


class TpvTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {

        $test = new Tpv();
        $this->assertInstanceOf('\Goteo\Library\Tpv', $test);

    }
}
