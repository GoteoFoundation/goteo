<?php

namespace Goteo\Repository\Tests;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Core\Model;
use Goteo\TestCase;
use Goteo\Model\DataSet;
use Goteo\Repository\DataSetRepository;

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
        $dataSets = $this->repository->getList([]);

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
            ->setUrl(Config::get('url.main'));

        $this->repository->save($dataSet);
        $this->assertCount(1, $this->repository->getList([]));
        return $dataSet;
    }

    /**
     * @depends testSaveDataSet
     */
    public function testDataSetExists(DataSet $dataSet): DataSet
    {
        $dbDataSet = $this->repository->getById($dataSet->getId());
        $this->assertEquals($dataSet->getId(), $dbDataSet->getId());
        return $dbDataSet;
    }

    /**
     * @depends testDataSetExists
     */
    public function deleteDataSet(DataSet $dataSet): void {
        $this->repository->delete($dataSet);
        $this->assertCount(0, $this->repository->getList([]));
    }
}
