<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Entity\ImpactData\ImpactDataItem;
use Goteo\Repository\ImpactItemRepository;
use Goteo\TestCase;

class ImpactDataItemRepositoryTest extends TestCase
{
    private ImpactDataItemRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ImpactDataItemRepository();
    }

    public function testEmptyOnGetListByImpactData() {
        $impactData = get_test_impact_data();

        $list = $this->repository->getListByImpactData($impactData);
        $this->assertEmpty($list);
    }

    public function testPersist(): ImpactDataItem
    {
        $impactData = get_test_impact_data();
        $impactItem = get_test_impact_item();
        $impactDataItem = new ImpactDataItem();
        $impactDataItem
            ->setImpactData($impactData)
            ->setImpactItem($impactItem);

        $this->repository->persist($impactDataItem);
        $list = $this->repository->getListByImpactData($impactData);
        $this->assertNotEmpty($list);
        return $impactDataItem;
    }

    /**
     * @depends testPersist
     */
    public function testDelete(ImpactDataItem $impactDataItem) {

        $this->repository->delete($impactDataItem);
        $this->assertEmpty($this->repository->getListByImpactData($impactDataItem->getImpactData()));
    }

    public static function tearDownAfterClass(): void
    {
        delete_test_impact_data();
        delete_test_impact_item();
        parent::tearDownAfterClass();
    }

}
