<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Web;

class WebTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Web();

        $this->assertInstanceOf('\Goteo\Model\User\Web', $converter);

        return $converter;
    }
}
