<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Cookie;

class CookieTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $ob = new Cookie();

        $this->assertInstanceOf('\Goteo\Application\Cookie', $ob);

        return $ob;
    }

    public function testStore() {
        $this->assertEquals('test-value', Cookie::store('test-key', 'test-value'));
        $this->assertTrue(Cookie::exists('test-key'));
    }

    public function testRetrieve() {
        $this->assertEquals('test-value', Cookie::get('test-key'));
    }

    public function testDelete() {
        $this->assertTrue(Cookie::del('test-key'));
        $this->assertFalse(Cookie::exists('test-key'));
    }
}
