<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Glossary;

class GlossaryTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Glossary();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Glossary', $controller);

        return $controller;
    }
}
