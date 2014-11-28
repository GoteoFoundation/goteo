<?php


namespace Goteo\Tests;

use Goteo\Library\WallFriends;

class WallFriendsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new WallFriends();

        $this->assertInstanceOf('\Goteo\Library\WallFriends', $converter);

        return $converter;
    }
}
