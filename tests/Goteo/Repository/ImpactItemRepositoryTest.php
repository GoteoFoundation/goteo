<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Entity\ImpactItem\ImpactItem;
use Goteo\TestCase;

class ImpactItemRepositoryTest extends TestCase
{
    private ImpactItemRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ImpactItemRepository();
    }

    public function testExceptionOnGetById(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("ImpactItem with id 1 not found");
        $this->repository->getById(1);
    }

    /**
     * @depends testExceptionOnGetById
     */
    public function testPersist(): ImpactItem
    {
        $errors = [];
        $impactItem = get_test_impact_item();
        $impactItem = $this->repository->persist($impactItem, $errors);
        $this->assertEmpty($errors, implode(',', $errors));
        $this->assertInstanceOf(ImpactItem::class, $impactItem);

        return $impactItem;
    }

    /**
     * @depends testPersist
     */
    public function testGetById(ImpactItem $impactItem): ImpactItem
    {
        $impactItemDB = $this->repository->getById($impactItem->getId());
        $this->assertInstanceOf(ImpactItem::class, $impactItemDB);
        return $impactItemDB;
    }

    /**
     * @depends testGetById
     */
    public function testDelete(ImpactItem $impactItem): void
    {
        $this->repository->delete($impactItem);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->getById($impactItem->getId());
    }
}
