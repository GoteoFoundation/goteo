<?php

namespace Goteo\Model\Tests;

use Goteo\TestCase;

use Goteo\Model\ImpactData;
use Goteo\Application\Exception\ModelNotFoundException;

class ImpactDataTest extends TestCase {

    private static $image = array(
                        'name' => 'test.png',
                        'type' => 'image/png',
                        'tmp_name' => '',
                        'error' => '',
                        'size' => 0);

    private static $data = array(
    	'id' => 12345,
    	'title' => 'Test post', 
    	'subtitle' => 'Test subtitle',
	    'description' => 'Test description'
    );


    public function testInstance() {
        \Goteo\Core\DB::cache(false);

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
    public function testGetImpactData(ImpactData $impact_data) {

    	$db_impact_data = ImpactData::get($impact_data->id);

    	$this->assertEquals($db_impact_data, $impact_data);

    }

    public function testNonExisting() {
        $this->expectException(ModelNotFoundException::class);

        ImpactData::get('');
    }

     /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
       try {
            $impact_data = ImpactData::get(self::$data['id']);
            $impact_data->dbDelete();
        }
        catch(ModelNotFoundException $e) {
        }
        catch(\PDOException $e) {
            error_log('PDOException on deleting test impact data! ' . $e->getMessage());
        }

        return false;
    }

}