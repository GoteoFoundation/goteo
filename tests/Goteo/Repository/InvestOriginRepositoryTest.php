<?php

namespace Goteo\Repository\Tests;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Entity\Invest\InvestOrigin;
use Goteo\TestCase;
use Goteo\Repository\InvestOriginRepository;

class InvestOriginRepositoryTest extends TestCase
{

    private InvestOriginRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InvestOriginRepository();
    }

    public function testExceptionOnGetByInvestId(): void {
        $id = 1;
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("InvestOrigin with invest_id {$id} not found");

        $this->repository->getByInvestId($id);

    }

    public function testGetList(): void {
        $investOrigins = $this->repository->getList();

        $this->assertIsArray($investOrigins);
        $this->assertCount(0, $investOrigins);
    }

    /**
     * @depends testGetList
     */
    public function testPersistInvestOrigin(): InvestOrigin {
        $investOrigin = new InvestOrigin();

        $invest = get_test_invest();

        $investOrigin->setInvestId($invest->id)
            ->setSource('https://goteo.org')
            ->setDetail('Blog post')
            ->setAllocated("post");

        $this->repository->persist($investOrigin);
        $investOriginCount = $this->repository->count();
        $this->assertEquals(1, $investOriginCount);
        return $investOrigin;
    }

    /**
     * @depends testPersistInvestOrigin
     */
    public function testInvestOriginExists(InvestOrigin $investOrigin): InvestOrigin
    {
        $dbInvestOrigin = $this->repository->getByInvestId($investOrigin->getInvestId());
        $this->assertEquals($investOrigin->getInvestId(), $dbInvestOrigin->getInvestId());
        return $investOrigin;
    }

    /**
     * @depends testPersistInvestOrigin
     */
    public function testUpdateInvestOrigin(InvestOrigin $investOrigin): InvestOrigin {
        $investOrigin->setSource('goteo');
        $investOrigin = $this->repository->persist($investOrigin);

        $dbInvestOrigin = $this->repository->getByInvestId($investOrigin->getInvestId());
        $this->assertEquals('goteo', $dbInvestOrigin->getSource());
        return $investOrigin;
    }

    /**
     * @depends testInvestOriginExists
     */
    public function testDeleteInvestOrigin(InvestOrigin $investOrigin): void
    {
        $this->repository->delete($investOrigin);

        $investOriginsCount = $this->repository->count();
        $this->assertEquals(0, $investOriginsCount);
    }

    static function tearDownAfterClass(): void {
        delete_test_project();
        delete_test_invest();
        delete_test_user();
        delete_test_node();
    }
}
