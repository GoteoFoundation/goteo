<?php


namespace Goteo\Library\Tests;

use Goteo\Library\SuperForm;

class SuperFormTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new SuperForm();

        $this->assertInstanceOf('\Goteo\Library\SuperForm', $converter);

        return $converter;
    }
}
