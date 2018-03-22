<?php

namespace Goteo\Payment\Tests;

use Goteo\Payment\Payment;
use Goteo\Payment\Method\AbstractPaymentMethod;

class MockPaymentMethod extends AbstractPaymentMethod {

}

class PaymentTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new Payment();

        $this->assertInstanceOf('Goteo\Payment\Payment', $ob);

        return $ob;

    }

    public function testAddMethod() {
        try {
            Payment::addMethod('stdClass');
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Payment\PaymentException', $e);
        }
        $this->assertEquals('mock', MockPaymentMethod::getId());
        Payment::addMethod('Goteo\Payment\Tests\MockPaymentMethod');
        $this->assertFalse(Payment::methodExists('mock'));
        Payment::addMethod('Goteo\Payment\Tests\MockPaymentMethod', true);
        $this->assertTrue(Payment::methodExists('mock'));
    }

    public function testGetMethods() {
        $methods = Payment::getMethods();
        $this->assertInternalType('array', $methods);
        $this->assertArrayHasKey('mock', $methods);
        $methods = Payment::getMethods(get_test_user());
        $this->assertContainsOnlyInstancesOf('Goteo\Payment\Method\PaymentMethodInterface', $methods);
        return $methods;
    }
   
    public function testGetMethod() {
        $this->assertInstanceOf('Goteo\Payment\Method\PaymentMethodInterface', Payment::getMethod('mock'));
    }

    /**
     * @depends testGetMethods
     */
    public function testDefaultMethod($methods) {
        Payment::defaultMethod('mock');
        $this->assertEquals('mock', Payment::defaultMethod());
    }

    public function testRemoveMethod() {
        $this->assertTrue(Payment::removeMethod('mock'));
        $this->assertArrayNotHasKey('mock', Payment::getMethods());
        Payment::addMethod('Goteo\Payment\Tests\MockPaymentMethod', true);
        $this->assertTrue(Payment::methodExists('mock'));
        $this->assertTrue(Payment::removeMethod('Goteo\Payment\Tests\MockPaymentMethod'));
        $this->assertArrayNotHasKey('mock', Payment::getMethods());
    }
}
