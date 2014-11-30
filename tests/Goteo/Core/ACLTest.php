<?php

namespace Goteo\Core\Tests;


use Goteo\Core\ACL;


class ACLTest extends \PHPUnit_Framework_TestCase {
    public function testInstance() {

        $test = new ACL();
        $this->assertInstanceOf('\Goteo\Core\ACL', $test);
    }
}
