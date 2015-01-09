<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Vip;

class VipTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Vip();

        $this->assertInstanceOf('\Goteo\Model\User\Vip', $converter);

        return $converter;
    }
}
