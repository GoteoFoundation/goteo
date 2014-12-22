<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Document();

        $this->assertInstanceOf('\Goteo\Controller\Document', $controller);

        return $controller;
    }
}
