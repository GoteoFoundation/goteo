<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Wordcount;

class WordcountTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Wordcount();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Wordcount', $controller);

        return $controller;
    }
}
