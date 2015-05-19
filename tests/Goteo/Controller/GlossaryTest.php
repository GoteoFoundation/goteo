<?php


namespace Goteo\Controller\Tests;

use Goteo\Controller\Glossary;

class GlossaryTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Glossary();

        $this->assertInstanceOf('\Goteo\Controller\Glossary', $controller);

        return $controller;
    }


}
