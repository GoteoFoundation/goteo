<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Currency;
use Goteo\Application\Session;

class CurrencyTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $cur = new Currency();

        $this->assertInstanceOf('Goteo\Application\Currency', $cur);
        $this->assertInstanceOf('Goteo\Library\Converter', $cur::getConverter());

        return $cur;
    }

    public function testAddCurrency() {
        $this->assertTrue(Currency::addCurrency(['id' => 'INR', 'symbol' => '₹', 'name' => 'Indian Rupee', 'thousands' => '.', 'decimal' => ',']));
        $this->assertContains('Indian Rupee', Currency::get('INR'));
        $this->assertContains('₹', Currency::get('INR'));
        $this->assertEquals('.', Currency::get('INR', 'thousands'));
        $this->assertEquals(',', Currency::get('INR', 'decimal'));
    }

    public function testDefaultCurrency() {
        Session::del('currency');
        $this->assertNotEquals(Currency::current(), Currency::get('INR', 'id'));
        Currency::setDefault('INR');
        $this->assertEquals(Currency::current(), Currency::get('INR', 'id'));
        try {
            Currency::setDefault('NON');
        } catch(\Exception $e) {
            $this->assertInstanceOf('RuntimeException', $e);
        }
        Session::store('currency', 'NON');
        $this->assertEquals(Currency::current(), Currency::get('INR', 'id'));
        Session::store('currency', 'EUR');
        Currency::setDefault('EUR');
        $this->assertEquals(Currency::current(), Currency::get('EUR', 'id'));
    }

    public function testAmountFormatCurrency() {

        $amount = 100000;

        // to euro
        Session::store('currency', 'EUR');
        $format = Currency::amountFormat($amount);
        print("[$amount/$format]\n");
        // format must have . for miliar
        $this->assertRegExp('/\d?\.{1}\d?/', $format, $format);
        $this->assertContains('€', $format);

        // to dollar
        Session::store('currency', 'USD');
        $format = Currency::amountFormat($amount);
        // echo $format."\n";
        // format must have , for miliar
        $this->assertRegExp('/\d?\,{1}\d?/', $format, $format);
        $this->assertContains('$', $format);

        // to pound
        Session::store('currency', 'GBP');
        $format = Currency::amountFormat($amount);
        // echo $format."\n";
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
