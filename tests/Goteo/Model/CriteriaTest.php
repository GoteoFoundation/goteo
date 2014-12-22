<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Criteria;

class CriteriaTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Criteria();

        $this->assertInstanceOf('\Goteo\Model\Criteria', $converter);

        return $converter;
    }
}
