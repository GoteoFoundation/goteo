<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Session;

class SessionTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new Session();

        $this->assertInstanceOf('\Goteo\Application\Session', $ob);

        return $ob;
    }
}
