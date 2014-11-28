<?php


namespace Goteo\Tests;

use Goteo\Library\SuperForm;

class SuperFormTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new SuperForm();

        $this->assertInstanceOf('\Goteo\Library\SuperForm', $converter);

        return $converter;
    }
}
