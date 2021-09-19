<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Search;

class SearchTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Search();

        $this->assertInstanceOf('\Goteo\Library\Search', $converter);

        return $converter;
    }
}
