<?php


namespace Goteo\Library\Tests;

use Goteo\Library\OAuth\SocialAuth;

class SocialAuthTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new SocialAuth();

        $this->assertInstanceOf('\Goteo\Library\OAuth\SocialAuth', $converter);

        return $converter;
    }
}
