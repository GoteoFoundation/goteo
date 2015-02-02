<?php

namespace Goteo\Util\Tests;

use Goteo\Util\Google\GoogleGeocoder;

class GoogleGeocoderTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new GoogleGeocoder();

        $this->assertInstanceOf('\Goteo\Util\Google\GoogleGeocoder', $ob);

        return $ob;

    }
}
