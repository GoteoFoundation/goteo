<?php

namespace Goteo\Payment\Tests;

use Goteo\Payment\Method;
use Goteo\Application\Config;

class CashPaymentMethodTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new CashPaymentMethod();

        $this->assertInstanceOf('Goteo\Payment\Method\CashPaymentMethod', $ob);

        return $ob;

    }

    public function testId() {
        $this->assertEquals('cash', CashPaymentMethod::getId());
    }

    public function testComissions() {
        Config::set('payments.cash.comissions.refunded.fixed', 0.1);
        Config::set('payments.cash.comissions.refunded.percent', 3);
        Config::set('payments.cash.comissions.charged.fixed', 0.2);
        Config::set('payments.cash.comissions.charged.percent', 1);
        // TODO: set invests and calc commissions
    }
}
