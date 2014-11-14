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


}
