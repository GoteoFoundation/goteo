<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Apikey;

class ApikeyTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $apikey = new Apikey();

        $this->assertInstanceOf('\Goteo\Model\User\Apikey', $apikey);

        return $apikey;
    }
}
