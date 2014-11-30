<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Search;

class SearchTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Search();

        $this->assertInstanceOf('\Goteo\Library\Search', $converter);

        return $converter;
    }
}
