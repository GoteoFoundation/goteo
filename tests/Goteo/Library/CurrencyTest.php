<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Currency;
use Goteo\Application\Session;

class CurrencyTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $cur = new Currency();

        $this->assertInstanceOf('\Goteo\Library\Currency', $cur);

        return $cur;
    }

    public function testAmountFormatCurrency() {

        $amount = 100000;

        // to euro
        Session::store('currency', 'EUR');
        $format = Currency::amount_format($amount);
        // echo $format."\n";
        // format must have . for miliar
        $this->assertRegExp('/\d?\.{1}\d?/', $format, $format);
        $this->assertContains('€', $format);

        // to dollar
        Session::store('currency', 'USD');
        $format = Currency::amount_format($amount);
        // echo $format."\n";
        // format must have , for miliar
        $this->assertRegExp('/\d?\,{1}\d?/', $format, $format);
        $this->assertContains('$', $format);

        // to pound
        Session::store('currency', 'GBP');
        $format = Currency::amount_format($amount);
        echo $format."\n";
        // format must have , for miliar
        $this->assertRegExp('/\d?\,{1}\d?/', $format, $format);
        $this->assertContains('£', $format);

        return true;
    }

    public function testRate() {
        Session::store('currency', 'USD');
        $rate = Currency::rate();

        $this->assertNotNull($rate);

    }

}
