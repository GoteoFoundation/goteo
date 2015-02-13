<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Accounts;

class AccountsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Accounts();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Accounts', $controller);

        return $controller;
    }
}
