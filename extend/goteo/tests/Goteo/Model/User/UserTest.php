<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Donor;

class DonorTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Donor();

        $this->assertInstanceOf('\Goteo\Model\User\Donor', $converter);

        return $converter;
    }
}
