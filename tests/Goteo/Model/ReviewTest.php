<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Review;

class ReviewTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Review();

        $this->assertInstanceOf('\Goteo\Model\Review', $converter);

        return $converter;
    }
}
