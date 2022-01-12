<?php

namespace Goteo\Repository\Tests;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Core\Model;
use Goteo\TestCase;
use Goteo\Entity\DataSet;
use Goteo\Repository\DataSetRepository;
use Goteo\Core\DB;

class DataSetRepositoryTest extends TestCase
{

    private DataSetRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new DataSetRepository();
    }

    public function testExceptionOnGetById(): void {
        $id = 1;
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("DataSet with id {$id} not found");

        $this->repository->getById($id);

    }

    public function testGetList(): void {
        $dataSets = $this->repository->getList();

        $this->assertIsArray($dataSets);
        $this->assertCount(0, $dataSets);
    }

    /**
     * @depends testGetList
     */
    public function testSaveDataSet(): DataSet {
        $dataSet = new DataSet();
        $dataSet->setId(1)
            ->setTitle('Data Set 1.')
            ->setDescription(' Description of DataSet 1')
            ->setUrl("https://duckduckgo.com/");

        $this->repository->save($dataSet);
        $dataSetCount = $this->repository->count();
        $this->assertEquals(1, $dataSetCount);
        return $dataSet;
    }

    /**
     * @depends testSaveDataSet
     */
    public function testDataSetExists(DataSet $dataSet): DataSet
    {
        $dbDataSet = $this->repository->getById($dataSet->getId());
        $this->assertEquals($dataSet->getId(), $dbDataSet->getId());
        return $dataSet;
    }

    /**
     * @depends testDataSetExists
     */
    public function testDeleteDataSet(DataSet $dataSet): void
    {
        print_r("delete function");
        $this->repository->delete($dataSet);

        $dataSetsCount = $this->repository->count();
        $this->assertEquals(0, $dataSetsCount);
    }
}
