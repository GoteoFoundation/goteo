<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Review;

class ReviewTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Review();

        $this->assertInstanceOf('\Goteo\Model\User\Review', $converter);

        return $converter;
    }
}
