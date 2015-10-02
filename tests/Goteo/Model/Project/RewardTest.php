<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Reward;

class RewardTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Reward();

        $this->assertInstanceOf('\Goteo\Model\Project\Reward', $converter);

        return $converter;
    }
}
