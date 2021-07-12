<?php


namespace Goteo\Library\Tests;

use Goteo\Library\OAuth\SocialAuth;

class SocialAuthTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new SocialAuth();

        $this->assertInstanceOf('\Goteo\Library\OAuth\SocialAuth', $converter);

        return $converter;
    }
}
