<?php

namespace Goteo\Repository;

use Goteo\Entity\ImpactItem\ImpactItem;
use Goteo\Entity\ImpactItem\ImpactItemFootprint;
use Goteo\TestCase;

class ImpactItemFootprintRepositoryTest extends TestCase
{
    private ImpactItemFootprintRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ImpactItemFootprintRepository();
    }

    public function testEmptyOnGetListByImpactData() {
        $footprint = get_test_footprint();

        $list = $this->repository->getListByFootprint($footprint);
        $this->assertEmpty($list);
    }

    public function testPersist(): ImpactItemFootprint
    {
        $footprint = get_test_footprint();
        $impactItem = get_test_impact_item();
        $impactItemFootprint = new ImpactItemFootprint();
        $impactItemFootprint
            ->setFootprint($footprint)
            ->setImpactItem($impactItem);

        $this->repository->persist($impactItemFootprint);
        $list = $this->repository->getListByFootprint($footprint);
        $this->assertNotEmpty($list);
        return $impactItemFootprint;
    }

    /**
     * @depends testPersist
     */
    public function testDelete(ImpactItemFootprint $impactItemFootprint) {

        $this->repository->delete($impactItemFootprint);
        $this->assertEmpty($this->repository->getListByFootprint($impactItemFootprint->getFootprint()));
    }

    public static function tearDownAfterClass(): void
    {
        delete_test_footprint();
        delete_test_impact_item();
        parent::tearDownAfterClass();
    }
}
