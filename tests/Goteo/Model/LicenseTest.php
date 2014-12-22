<?php


namespace Goteo\Model\Tests;

use Goteo\Model\License;

class LicenseTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new License();

        $this->assertInstanceOf('\Goteo\Model\License', $converter);

        return $converter;
    }
}
