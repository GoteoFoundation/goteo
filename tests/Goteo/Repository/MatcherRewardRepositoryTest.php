<?php

namespace Goteo\Repository\Tests;

use Goteo\Application\Exception\ModelException;
use Goteo\Entity\Matcher\MatcherReward;
use Goteo\Model\Matcher;
use Goteo\Model\Project\Reward;
use Goteo\Repository\MatcherRewardRepository;
use Goteo\TestCase;

class MatcherRewardRepositoryTest extends TestCase
{

    private static MatcherRewardRepository $repository;
    private static Matcher $matcher;
    private static Reward $reward;

    static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$repository = new MatcherRewardRepository();
        self::$matcher = get_test_matcher();
        self::$reward = get_test_reward();
    }

    public function testCountZeroWhenMatcherDoesNotExist()
    {
        $this->assertEquals(0, self::$repository->count(self::$matcher));
    }

    public function testGetListEmpty()
    {
        $this->assertCount(0, self::$repository->getListByMatcher(self::$matcher));
    }

    public function testPersist(): MatcherReward
    {
        $matcherReward = new MatcherReward();
        $matcherReward->setMatcher(self::$matcher)->setReward(self::$reward);
        self::$repository->persist($matcherReward);

        $this->assertEquals(1, self::$repository->count(self::$matcher));
        return $matcherReward;
    }

    /**
     * @depends testPersist
     */
    public function testGetListByMatcher()
    {
        $this->assertCount(1, self::$repository->getListByMatcher(self::$matcher));
    }

    /**
     * @depends testPersist
     */
    public function testGetListByMatcherAndProject()
    {
        $project = self::$reward->getProject();
        $this->assertCount(1, self::$repository->getListByMatcherAndProject(self::$matcher, $project));
    }

    /**
     * @depends testPersist
     */
    public function testExists(MatcherReward $matcherReward)
    {
        $this->assertTrue(self::$repository->exists($matcherReward->getMatcher(), $matcherReward->getReward()));
    }

    /**
     * @depends testPersist
     */
    public function testDelete(MatcherReward $matcherReward)
    {
        self::$repository->delete($matcherReward);
        $this->assertEquals(0, self::$repository->count($matcherReward->getMatcher()));
    }

    static function tearDownAfterClass(): void
    {
        delete_test_reward();
        delete_test_matcher();
    }
}
