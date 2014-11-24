<?php

namespace Goteo\Tests;

use Goteo\Core\Model,
    Goteo\Core\DB,
    Goteo\Library\Cacher;

class ModelTest extends \PHPUnit_Framework_TestCase {

    public function testInstanceQuery() {
        $query = Model::query("SELECT 1");
        $this->assertInstanceOf('\PDOStatement', $query);
        $this->assertInstanceOf('\Goteo\Library\Cacher', $query->cache);
        $this->assertFalse(DB::cache(false));
        $this->assertEquals(SQL_CACHE_TIME, $query->cache->getCacheTime());
        $this->assertTrue(DB::cache(true));

    }

    public function testQuery() {
        $sql = "SELECT RAND() as num";
        $query = Model::query($sql);
        $res1 = $query->fetchColumn();
        $this->assertLessThanOrEqual(1, $res1);
        $this->assertGreaterThan(0, $res1);
        usleep(5000);

        $query = Model::query($sql);
        $res2 = $query->fetchColumn();

        $this->assertEquals($res1, $res2);
        //wait until cache expires
        sleep(SQL_CACHE_TIME + 1);

        $query = Model::query($sql);
        $res2 = $query->fetchColumn();
        $this->assertNotEquals($res1, $res2);

    }

    public function testInvalidateCache() {
        $sql = "SELECT RAND() as num";
        $query = Model::query($sql);
        $res1 = $query->fetchColumn();
        $this->assertLessThanOrEqual(1, $res1);
        $this->assertGreaterThan(0, $res1);
        usleep(5000);
        Model::cleanCache();

        $query = Model::query($sql);
        $res2 = $query->fetchColumn();

        $this->assertNotEquals($res1, $res2);
        //wait until cache expires
    }

    public function testIdealiza() {
        $text = "àẁèỳśẅçÇ";
        $this->assertEquals('aweyswcc', Model::idealiza($text));
    }
}
