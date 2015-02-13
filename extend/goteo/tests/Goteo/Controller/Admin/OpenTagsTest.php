<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\OpenTags;

class OpenTagsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new OpenTags();

        $this->assertInstanceOf('\Goteo\Controller\Admin\OpenTags', $controller);

        return $controller;
    }
}
