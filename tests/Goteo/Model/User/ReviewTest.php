<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Review;

class ReviewTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Review();

        $this->assertInstanceOf('\Goteo\Model\User\Review', $converter);

        return $converter;
    }
}
