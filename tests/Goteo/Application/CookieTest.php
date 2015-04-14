<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Cookie;

class CookieTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new Cookie();

        $this->assertInstanceOf('\Goteo\Application\Cookie', $ob);

        return $ob;
    }

    public function testStore() {
        $this->assertEquals(Cookie::store('test-key', 'test-value'), 'test-value');
        $this->assertTrue(Cookie::exists('test-key'));
    }

    public function testRetrieve() {
        $this->assertEquals(Cookie::get('test-key'), 'test-value');
    }

    public function testDelete() {
        $this->assertTrue(Cookie::del('test-key'));
        $this->assertFalse(Cookie::exists('test-key'));
    }
}
