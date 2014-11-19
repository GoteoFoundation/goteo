<?php


namespace Goteo\Tests;

use Goteo\Library\Currency;

class CurrencyTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $cur = new Currency();

        $this->assertInstanceOf('\Goteo\Library\Currency', $cur);

        return $cur;
    }

    /**
     * [testInstance description]
     * @depends testInstance
     */
    public function testGetCurrency($cur) {

        //test euro
        $res1 = $cur->getRates('EUR');
        $this->assertArrayHasKey('USD', $res1);

        usleep(100);

        //test de cache
        $res2 = $cur->getRates('EUR');
        $this->assertEquals($res1, $res2);

        //invalidar cache
        $cur->cleanCache();

        //TODO...
        //test USD
        // $res = $cur->getRates('USD');
        // print_r($res);
        // $this->assertArrayHasKey('EUR', $res);

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
        $this->assertEquals($res_force, strtoupper($ccy1));

        return true;
    }


}
