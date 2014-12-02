<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Community;

class CommunityTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Community();

        $this->assertInstanceOf('\Goteo\Controller\Community', $controller);

        return $controller;
    }
}
