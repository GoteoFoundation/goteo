<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Template();

        $this->assertInstanceOf('\Goteo\Library\Template', $converter);

        return $converter;
    }
}
