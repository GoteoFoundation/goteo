<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Listing;

class ListingTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Listing();

        $this->assertInstanceOf('\Goteo\Library\Listing', $converter);

        return $converter;
    }
}
