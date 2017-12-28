<?php

namespace Goteo\Core\Tests;

use Goteo\Core\DB,
    Goteo\Library\Cacher;

class DBTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {
        DB::cache(false);
        $db = new DB();
        $this->assertInstanceOf('Goteo\Core\DB', $db);
        $this->assertInstanceOf('\PDO', $db);
        return $db;
    }

    /**
     * Testing a simple query
     * @depends testInstance
     */
    public function testSimpleSelect($db){
        $sql1 = "SELECT 1 as num";
        $sql2 = "SELECT 2 as num";
        // $sql = "SELECT id FROM project LIMIT 1";

        $query = $db->prepare($sql1);

        $this->assertInstanceOf('\PDOStatement', $query);
        $this->assertTrue($query->execute());

        $res1 = $query->fetchColumn();

        $query = $db->prepare($sql2);
        $query->execute();
        $res2 = $query->fetchColumn();

        $this->assertEquals(1, $res1);
        $this->assertEquals(2, $res2);
        $this->assertNotEquals($res1, $res2);

        return $db;
    }

    /**
     * Testing a non cached sql
     */
    public function testNonCachedSelect(){
        $db = new DB(new Cacher('sql', 2));
        $this->assertFalse(DB::cache());

        $sql = "SELECT RAND() as num";

        $query = $db->prepare($sql);
        $query->execute();
        $res1 = $query->fetchColumn();
        usleep(5000);
        $query->execute();
        $res2 = $query->fetchColumn();

        $this->assertNotEquals($res1, $res2);

        return $db;
    }

    /**
     * Testing statemments and caches instances
     * @depends testNonCachedSelect
     */
    public function testStatement($db) {
        DB::cache(true);
        $this->assertTrue(DB::cache());
        $query = $db->prepare('SELECT 1');
        $this->assertEquals($query->cache_time, 2); //specified on constructor

        $query->cacheTime(23);

        $this->assertEquals($query->cache_time, 23);

        $query->cacheTime(6);

        $this->assertEquals($query->cache_time, 6);

        return $db;
    }

    /**
     * Testing cached sql
     */
    public function testCachedSelect(){
        $db = new DB(new Cacher('sql', 1));
        DB::cache(true);
        $this->assertTrue(DB::cache());

        $sql = "SELECT RAND() as num FROM node";

        $query = $db->prepare($sql);
        $this->assertEquals(1, $query->cacheTime());
        $query->execute();
        $res1 = $query->fetchColumn();

        usleep(500000);

        $db = new DB(new Cacher('sql', 1));
        $query = $db->prepare($sql);
        $query->execute();
        $res2 = $query->fetchColumn();

        $this->assertEquals($res1, $res2);

        usleep(1500001);

        $query = $db->prepare($sql);
        $query->execute();
        $res2 = $query->fetchColumn();

        $this->assertNotEquals($res1, $res2);

        return $db;
    }

    /**
     * Testing a simple query
     * @depends testNonCachedSelect
     */
    public function testInvalidateCache($db){

        DB::cache(true);
        $this->assertTrue(DB::cache());

        $sql = "SELECT RAND() as num";

        $query = $db->prepare($sql);
        $query->cacheTime(5);
        $this->assertEquals(5, $query->cacheTime());
        $query->execute();
        $res1 = $query->fetchColumn();

        usleep(500);
        $db->cleanCache();

        $query = $db->prepare($sql);
        $query->execute();
        $query->cacheTime(1);
        $res2 = $query->fetchColumn();

        $this->assertNotEquals($res1, $res2);

        $db->cleanCache();

        return $db;
    }

    //TODO: insert test, update test
}
