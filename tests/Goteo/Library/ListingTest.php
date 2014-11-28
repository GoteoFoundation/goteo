<?php


namespace Goteo\Tests;

use Goteo\Library\Listing;

class ListingTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Listing();

        $this->assertInstanceOf('\Goteo\Library\Listing', $converter);

        return $converter;
    }
}
