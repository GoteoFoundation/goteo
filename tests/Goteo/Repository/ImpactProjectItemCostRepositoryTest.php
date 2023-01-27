<?php

namespace Goteo\Repository;

use Goteo\TestCase;

class ImpactProjectItemCostRepositoryTest extends TestCase
{
    private ImpactProjectItemCostRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $repository = new ImpactProjectItemCostRepository();
    }
}
