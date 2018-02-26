<?php

namespace Goteo\Library\Tests;

use Goteo\Library\Converter;

class ConverterTest extends \PHPUnit_Framework_TestCase {

	public function testInstance() {

		$converter = new Converter();

		$this->assertInstanceOf('\Goteo\Library\Converter', $converter);

		return $converter;
	}

	/**
	 * doRequest is a private method
	 *
	 * https://sebastian-bergmann.de/archives/881-Testing-Your-Privates.html
	 *
	 * @covers Foo::doSomethingPrivate
	 * @depends testInstance
	 *
	 *
	public function testDoRequest($converter) {

	$method = new \ReflectionMethod(
	'Converter', 'doRequest'
	);

	$method->setAccessible(TRUE);

	$converter = new Converter;

	// banco central europeo
	$params = $method->invokeArgs($converter, array(Converter::ECB_URL));
	$method->invoke($converter, $params);

	// the money converter
	$params = $method->invokeArgs($converter, array(Converter::TMC_URL));
	$method->invoke($converter, $params);

	return true;
	}
	 */

	/**
	 * [getData]
	 * @depends testInstance
	 *
	 *
	public function testGetData($converter) {

	$method = new \ReflectionMethod(
	'Converter', 'getData'
	);

	$method->setAccessible(TRUE);

	$converter = new Converter;

	$params = $method->invokeArgs($converter, array('EUR'));
	$method->invoke($converter, $params);

	$params = $method->invokeArgs($converter, array('USD'));
	$method->invoke($converter, $params);

	return true;
	}
	 */

	/**
	 * @depends testInstance
	 */
	public function testGetRates($converter) {
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

        usleep(100);

        //test de cache
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
    public function testGetConverterUSD($converter) {

		//test dollar
		$res1 = $converter->getRates('USD');
		$this->assertArrayHasKey('EUR', $res1);

		usleep(100);

		//test de cache
		$res2 = $converter->getRates('USD');
		$this->assertEquals($res1, $res2);

		//invalidar cache
		$converter->cleanCache();

		return $converter;
	}
    */
}
