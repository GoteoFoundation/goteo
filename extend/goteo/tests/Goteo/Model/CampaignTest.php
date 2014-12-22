<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Campaign;

class CampaignTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Campaign();

        $this->assertInstanceOf('\Goteo\Model\Campaign', $converter);

        return $converter;
    }
}
