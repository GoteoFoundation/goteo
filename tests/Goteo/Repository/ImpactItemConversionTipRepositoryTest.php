<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Entity\ImpactItem\ImpactItemConversionTip;
use Goteo\TestCase;

class ImpactItemConversionTipRepositoryTest extends TestCase
{
    private ImpactItemConversionTipRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ImpactItemConversionTipRepository();
    }

    public function testGetThrowsException(): void
    {
        $impactItem = get_test_impact_item();

        $this->expectException(ModelNotFoundException::class);
        $this->repository->getByImpactItem($impactItem);
    }

    public function testPersist(): ImpactItemConversionTip
    {
        $data = [
            'rate_tip_description' => 'test rate tip description',
            'reference' => 'test reference'
        ];

        $impactItem = get_test_impact_item();

        $impactItemConversionTip = new ImpactItemConversionTip();
        $impactItemConversionTip
            ->setImpactItem($impactItem)
            ->setRateTipDescription($data['rate_tip_description'])
            ->setReference($data['reference']);

        $errors = [];
        $this->repository->persist($impactItemConversionTip, $errors);
        $this->assertEmpty($errors);

        return $impactItemConversionTip;
    }

    /**
     * @depends testPersist
     */
    public function testGetByImpactItem(ImpactItemConversionTip $impactItemConversionTip):ImpactItemConversionTip
    {
        $this->assertTrue(true);
        return $impactItemConversionTip;
    }

    /**
     * @depends testPersist
     */
    public function testDelete(ImpactItemConversionTip $impactItemConversionTip) {
        $this->repository->delete($impactItemConversionTip);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->getByImpactItem($impactItemConversionTip->getImpactItem());
    }

}
