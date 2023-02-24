<?php

namespace Goteo\Repository;

use Goteo\Entity\ImpactItem\ImpactProjectItem;
use Goteo\TestCase;

class ImpactProjectItemRepositoryTest extends TestCase
{
    private ImpactProjectItemRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ImpactProjectItemRepository();
    }

    public function testGetListProjectIsEmpty(): void
    {
        $project = get_test_project();
        $this->assertEmpty($this->repository->getListByProject($project));
    }

    public function testPersist(): ImpactProjectItem
    {
        $impactItem = get_test_impact_item();
        $project = get_test_project();

        $impactProjectItem = new ImpactProjectItem();
        $impactProjectItem
            ->setProject($project)
            ->setImpactItem($impactItem);

        $errors = [];
        $this->repository->persist($impactProjectItem, $errors);
        $this->assertEmpty($errors, implode(',', $errors));

        return $impactProjectItem;
    }

    /**
     * @depends testPersist
     */
    public function testDelete(ImpactProjectItem $impactProjectItem) {
        $this->repository->delete($impactProjectItem);

        $this->assertEmpty($this->repository->getListByProject($impactProjectItem->getProject()));
    }

    static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        delete_test_project();
    }
}
