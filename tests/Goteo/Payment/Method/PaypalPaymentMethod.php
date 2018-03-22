<?php

namespace Goteo\Payment\Tests;

use Goteo\Payment\Method;

class PaypalPaymentMethodTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new PaypalPaymentMethod();

        $this->assertInstanceOf('Goteo\Payment\Method\PaypalPaymentMethod', $ob);

        return $ob;

    }
}
