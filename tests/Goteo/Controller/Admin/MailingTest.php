<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Mailing;

class MailingTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Mailing();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Mailing', $controller);

        return $controller;
    }
}
