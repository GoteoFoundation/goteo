<?php

namespace Goteo\Repository\Tests;

use Goteo\Application\Exception\ModelException;
use Goteo\Entity\ImpactData\ImpactDataProject;
use Goteo\Model\ImpactData;
use Goteo\Model\Project;
use Goteo\Repository\ImpactDataProjectRepository;
use Goteo\TestCase;
use function PHPUnit\Framework\assertInstanceOf;

class ImpactDataProjectRepositoryTest extends TestCase
{

    private static ImpactDataProjectRepository $repository;
    private static Project $project;
    private static ImpactData $impactData;

    public function setUp(): void
    {
        parent::setUp();
        self::$repository = new ImpactDataProjectRepository();
    }

    public function testGetListByProject()
    {
        $project = get_test_project();

        $list = self::$repository->getListByProject($project);
        $this->assertEmpty($list);
    }

    public function testCountZero()
    {
        $project = get_test_project();

        $this->assertEquals(0, self::$repository->count($project));
    }

    public function testPersists(): ImpactDataProject
    {
        $impactData = get_test_impact_data();
        $project = get_test_project();

        $impactDataProject = new ImpactDataProject();
        $impactDataProject
                ->setImpactData($impactData)
                ->setProject($project);

        $errors = [];
        $this->assertEquals($impactDataProject, self::$repository->persist($impactDataProject, $errors), implode(',', $errors));
        $this->assertEmpty($errors);

        return $impactDataProject;
    }

    /**
     * @depends testPersists
     */
    public function testExistsRelationBetweenImpactDataAndProject(ImpactDataProject $impactDataProject)
    {
        $impactData = get_test_impact_data();
        $project = get_test_project();
        $this->assertTrue(self::$repository->exists($impactData, $project));

        return $impactDataProject;
    }

    /**
     * @depends testExistsRelationBetweenImpactDataAndProject
     */
    public function testDeleteImpactDataProject(ImpactDataProject $impactDataProject)
    {
        self::$repository->delete($impactDataProject);
        $this->assertEquals(0, self::$repository->count($impactDataProject->getProject()));
    }

    public static function tearDownAfterClass(): void
    {
        delete_test_impact_data();
        delete_test_project();
        parent::tearDownAfterClass();
    }
}
