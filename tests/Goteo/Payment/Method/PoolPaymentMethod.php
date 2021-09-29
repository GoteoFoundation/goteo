<?php

namespace Goteo\Payment\Tests;

use Goteo\Payment\Method;

class PoolPaymentMethodTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $ob = new PoolPaymentMethod();

        $this->assertInstanceOf('Goteo\Payment\Method\PoolPaymentMethod', $ob);

        return $ob;

    }
}
