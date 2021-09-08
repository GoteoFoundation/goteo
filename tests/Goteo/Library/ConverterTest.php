<?php

namespace Goteo\Library\Tests;

use Goteo\Library\Converter;
use Goteo\Library\ConverterReader;

class ConverterTest extends \PHPUnit\Framework\TestCase {
    private $reader;

	public function testInstance() {

		$converter = new Converter();
        $reader =  $this->createMock(ConverterReader::class);
        $reader->method('get')
             ->willReturn(file_get_contents(__DIR__ . "/eurofxref-daily.xml"));

        $converter->setReader($reader);
		$this->assertInstanceOf('\Goteo\Library\Converter', $converter);

		return $converter;
	}

	/**
	 * @depends testInstance
	 */
	public function testGetRates($converter) {
        // Configure the stub for EUR.
		$rates = $converter->getRates('EUR');
		$this->assertArrayHasKey('USD', $rates);

		return $converter;
	}

	/**
	 * @depends testGetRates
	 */
    public function testGetConverterEUR($converter) {

        //test euro
        $res1 = $converter->getRates('EUR');
        $this->assertArrayHasKey('USD', $res1);

        //test de cache
        $key = $converter->getCache()->getKey('EUR', 'rates');
        $cache = $converter->getCache()->retrieve($key);
        $this->assertEquals($res1, $cache);

        $res2 = $converter->getRates('EUR');
        $this->assertEquals($res1, $res2);

    	//invalidar cache
		$converter->cleanCache();
    }

    /**
     * TODO: This test fails in Travis
     *
     * @depends testGetRates
     *
    */
    public function testGetConverterUSD($converter) {
        // Configure the stub for USD.
        $reader =  $this->createMock(ConverterReader::class);
        $reader->method('get')
             ->willReturn(file_get_contents(__DIR__ . "/USD.xml"));
        $converter->setReader($reader);

		//test dollar
		$res1 = $converter->getRates('USD');
		$this->assertArrayHasKey('EUR', $res1);

		//test de cache

        $key = $converter->getCache()->getKey('USD', 'rates');
        $cache = $converter->getCache()->retrieve($key);
        $this->assertEquals($res1, $cache);

		$res2 = $converter->getRates('USD');
		$this->assertEquals($res1, $res2);

		//invalidar cache
		$converter->cleanCache();

		return $converter;
	}
}
