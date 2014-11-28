<?php


namespace Goteo\Tests;

use Goteo\Library\Lang;

class LangTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Lang();

        $this->assertInstanceOf('\Goteo\Library\Lang', $converter);

        return $converter;
    }
}
