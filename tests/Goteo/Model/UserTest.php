<?php


namespace Goteo\Model\Tests;

use Goteo\Model\User;

class UserTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new User();

        $this->assertInstanceOf('\Goteo\Model\User', $converter);

        return $converter;
    }
}
