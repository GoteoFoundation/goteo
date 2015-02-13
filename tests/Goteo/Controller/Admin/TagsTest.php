<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Tags;

class TagsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Tags();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Tags', $controller);

        return $controller;
    }
}
