<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Session;

class SessionTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $ob = new Session();

        $this->assertInstanceOf('\Goteo\Application\Session', $ob);

        return $ob;
    }

    public function testStore() {
        Session::start('test', 3600);
        Session::store('test-key', 'test-value');
        $this->assertTrue(Session::exists('test-key'));
        $this->assertEquals('test-value', Session::get('test-key'));
    }

    public function testRetrieve() {
        $this->assertEquals('test-value', Session::get('test-key'));
    }

    public function testDelete() {
        $this->assertTrue(Session::del('test-key'));
        $this->assertFalse(Session::exists('test-key'));
    }

    public function testGetAndDelete() {
        Session::store('test-key', 'test-value');
        $this->assertEquals('test-value', Session::get('test-key'));
        $this->assertTrue(Session::exists('test-key'));
        $this->assertEquals('test-value', Session::getAndDel('test-key'));
        $this->assertFalse(Session::exists('test-key'));
    }

    public function testDestroy() {
        Session::start('test', 3600);
        Session::store('test-key-2', 'test-value-2');
        $this->assertEquals('test-value-2', Session::get('test-key-2'));
    }

    public function testExpire() {
        Session::start('test');
        Session::store('test-key-3', 'test-value-3');
        $this->assertEquals('test-value-3', Session::get('test-key-3'));
    }
}
