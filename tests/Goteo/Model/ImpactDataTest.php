<?php

namespace Goteo\Model\Tests;

use Goteo\Core\DB;
use Goteo\TestCase;

use Goteo\Model\ImpactData;
use Goteo\Application\Exception\ModelNotFoundException;

class ImpactDataTest extends TestCase {

    private static array $image = [
                        'name' => 'test.png',
                        'type' => 'image/png',
                        'tmp_name' => '',
                        'error' => '',
                        'size' => 0];

    private static array $data = [
    	'title' => 'Test post',
	    'data' => 'Test data',
        'data_unit' => 'Test unit',
    	'description' => 'Test description'
    ];


    public function testInstance(): ImpactData
    {
        DB::cache(false);

        $ob = new ImpactData();
        $this->assertInstanceOf(ImpactData::class, $ob);

        return $ob;
    }

     /**
     * @depends testInstance
     */
    public function testValidate(ImpactData $ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    public function testCreate(): ImpactData {
    	$ob = new ImpactData(self::$data);

    	$this->assertTrue($ob->validate($errors));
    	$this->assertTrue($ob->save($errors), print_r($errors, 1));

    	$db_impact_data = ImpactData::get($ob->id);
    	$this->assertEquals($db_impact_data, $ob);

    	return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testGetImpactData(ImpactData $impact_data): ImpactData
    {
    	$db_impact_data = ImpactData::get($impact_data->id);

    	$this->assertEquals($db_impact_data, $impact_data);

        return $impact_data;

    }

    public function testNonExisting() {
        $this->expectException(ModelNotFoundException::class);

        ImpactData::get('');
    }

    /**
     * @depends testGetImpactData
     */
    public function testList() {
        $impact_data_list = ImpactData::getList();

        $this->assertCount(1, $impact_data_list);
    }

    /**
     * @depends testGetImpactData
     */
    public function testGetListBySource()
    {
        $impact_data_list = ImpactData::getList(['source' => ImpactData::SOURCE_ITEM]);

        $this->assertCount(1, $impact_data_list);

        $impact_data_list = ImpactData::getList(['source' => ImpactData::SOURCE_PROJECT]);
        $this->assertEmpty($impact_data_list);
    }

    public function testGetListByType() {
        $impact_data_list = ImpactData::getList(['type' => ImpactData::TYPE_ESTIMATION]);
        $this->assertCount(1, $impact_data_list);

        $impact_data_list = ImpactData::getList(['type' => ImpactData::TYPE_REAL]);
        $this->assertEmpty($impact_data_list);
    }

    /**
     * @depends testGetImpactData
     */
    public function testRemove(ImpactData $impactData) {

        $impactDataCount = ImpactData::getList([],0,0,true);

        $response = $impactData->dbDelete();

        $this->assertTrue($response);
        $this->assertCount($impactDataCount - 1, ImpactData::getList());
    }


    static function tearDownAfterClass(): void {
       try {
            $count = ImpactData::getList([],0,0,true);
            $impact_data_list = ImpactData::getList([], 0, $count);
            foreach ($impact_data_list as $impact_data) {
                $impact_data->dbDelete();
            }
        }
        catch(\PDOException $e) {
            error_log('PDOException on deleting test impact data! ' . $e->getMessage());
        }
    }

}
