<?php


namespace Goteo\Controller\Dashboard\Tests;

use Goteo\Controller\Dashboard\Profile;

class ProfileTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Profile();

        $this->assertInstanceOf('\Goteo\Controller\Dashboard\Profile', $controller);

        return $controller;
    }
}
