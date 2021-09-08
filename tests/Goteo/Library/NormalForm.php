<?php


namespace Goteo\Library\Tests;

use Goteo\Library\NormalForm;

class NormalFormTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new NormalForm();

        $this->assertInstanceOf('\Goteo\Library\NormalForm', $converter);

        return $converter;
    }
}
