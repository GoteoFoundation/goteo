<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Sponsor;

class SponsorTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Sponsor();

        $this->assertInstanceOf('\Goteo\Model\Sponsor', $converter);

        return $converter;
    }
}
