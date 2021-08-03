<?php

namespace Goteo\Payment\Tests;

use Exception;
use Goteo\Payment\Method\PaymentMethodInterface;
use Goteo\Payment\Payment;
use Goteo\Payment\Method\AbstractPaymentMethod;
use Goteo\Payment\PaymentException;

class MockPaymentMethod extends AbstractPaymentMethod {

}

class PaymentTest extends \PHPUnit\Framework\TestCase {

    public function testInstance(): Payment
    {
        $ob = new Payment();

        $this->assertInstanceOf('Goteo\Payment\Payment', $ob);

        return $ob;
    }

    public function testAddMethod() {
        try {
            Payment::addMethod('stdClass');
        } catch(Exception $e) {
            $this->assertInstanceOf(PaymentException::class, $e);
        }
        $this->assertEquals('mock', MockPaymentMethod::getId());
        Payment::addMethod(MockPaymentMethod::class);
        $this->assertFalse(Payment::methodExists('mock'));
        Payment::addMethod(MockPaymentMethod::class, true);
        $this->assertTrue(Payment::methodExists('mock'));
    }

    public function testGetMethods(): array
    {
        $methods = Payment::getMethods();
        $this->assertIsArray($methods);
        $this->assertArrayHasKey('mock', $methods);
        $methods = Payment::getMethods(get_test_user());
        $this->assertContainsOnlyInstancesOf(PaymentMethodInterface::class, $methods);
        return $methods;
    }

    public function testGetMethod() {
        $this->assertInstanceOf(PaymentMethodInterface::class, Payment::getMethod('mock'));
    }

    /**
     * @depends testGetMethods
     */
    public function testDefaultMethod() {
        Payment::defaultMethod('mock');
        $this->assertEquals('mock', Payment::defaultMethod());
    }

    public function testRemoveMethod() {
        $this->assertTrue(Payment::removeMethod('mock'));
        $this->assertArrayNotHasKey('mock', Payment::getMethods());
        Payment::addMethod(MockPaymentMethod::class, true);
        $this->assertTrue(Payment::methodExists('mock'));
        $this->assertTrue(Payment::removeMethod(MockPaymentMethod::class));
        $this->assertArrayNotHasKey('mock', Payment::getMethods());
    }
}
