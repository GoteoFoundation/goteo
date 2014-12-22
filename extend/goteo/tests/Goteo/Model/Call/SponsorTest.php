<?php


namespace Goteo\Model\Call\Tests;

use Goteo\Model\Call\Sponsor;

class SponsorTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Sponsor();

        $this->assertInstanceOf('\Goteo\Model\Call\Sponsor', $converter);

        return $converter;
    }
}
