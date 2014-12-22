<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Glossary;

class GlossaryTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Glossary();

        $this->assertInstanceOf('\Goteo\Model\Glossary', $converter);

        return $converter;
    }
}
