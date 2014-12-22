<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Account;

class AccountTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Account();

        $this->assertInstanceOf('\Goteo\Model\Project\Account', $converter);

        return $converter;
    }
}
