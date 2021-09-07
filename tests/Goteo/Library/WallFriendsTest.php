<?php


namespace Goteo\Library\Tests;

use Goteo\Library\WallFriends;
use Goteo\Model\Project;

class WallFriendsTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new WallFriends(new Project());

        $this->assertInstanceOf('\Goteo\Library\WallFriends', $converter);

        return $converter;
    }
}
