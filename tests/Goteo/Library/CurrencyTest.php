<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Currency;

class CurrencyTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $cur = new Currency();

        $this->assertInstanceOf('\Goteo\Library\Currency', $cur);

        return $cur;
    }

    /**
     * Test the currency constant setting
     */
    public function testSetCurrency() {

        // testing currency
        $ccy0 = 'eur';
        $ccy1 = 'usd';
        $ccy2 = 'gbp';

        // set default
        $res_default = Currency::set();
        $this->assertEquals($res_default, Currency::DEFAULT_CURRENCY); // EUR


        // set from force
        $res_force = Currency::set($ccy1); // USD
        $this->assertEquals($res_force, strtoupper($ccy1));

        // set from COOKIE
        $_COOKIE['currency'] = $ccy2;
        $res_cookie = Currency::set();
        $this->assertEquals($res_cookie, strtoupper($ccy2));

        // set from SESSION
        $_SESSION['currency'] = $ccy0;
        $res_session = Currency::set();
        $this->assertEquals($res_session, strtoupper($ccy0));

        // set from GET
        $_GET['currency'] = $ccy1;
        $res_get = Currency::set();
        $this->assertEquals($res_get, strtoupper($ccy1));

        return true;
    }


    public function testAmountFormatCurrency() {

        $amount = 100000;

        // to euro
        $_SESSION['currency'] = 'eur';
        $format = Currency::amount_format($amount);
        // echo $format."\n";
        // format must have . for miliar
        $this->assertRegExp('/\d?.{1}\d?/', $format, $format);


        // to dollar
        $_SESSION['currency'] = 'usd';
        $format = Currency::amount_format($amount);
        // echo $format."\n";
        // format must have , for miliar
        $this->assertRegExp('/\d?\,{1}\d?/', $format, $format);

        // to pound
        $_SESSION['currency'] = 'gbp';
        $format = Currency::amount_format($amount);
        // echo $format."\n";
        // format must have , for miliar
        $this->assertRegExp('/\d?\,{1}\d?/', $format, $format);

        return true;
    }

    public function testRate() {
        $_SESSION['currency'] = 'usd';
        $rate = Currency::rate();

        $this->assertNotNull($rate);

    }

}
