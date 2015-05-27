<?php

namespace Goteo\Core\Tests;

use Goteo\Core\Model,
    Goteo\Core\DB,
    Goteo\Library\Cacher;

class MockModel extends Model {
    public $id;
    public $uniq;
    public $name;
    public static function get ($id) {}
    public function save (&$errors = array()) {}
    public function validate (&$errors = array()) {}
}

class ModelTest extends \PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        DB::cache(false);
    }

    public function testGetTable() {
        $mock = new MockModel();
        $this->assertEquals('mockmodel', $mock->getTable());
        return $mock;
    }

    /**
     * @depends testGetTable
     */
    public function testIdealiza($mock) {
        $text = "àẁèỳśẅçÇ h";
        $this->assertEquals('aweyswcc-h', Model::idealiza($text));
        $this->assertEquals('aweyswcc-h', $mock::idealiza($text));
        return $mock;
    }

    /**
     * @depends testGetTable
     */
    public function testInstanceQuery($mock) {
        $query = Model::query("SELECT 1");
        $this->assertInstanceOf('\PDOStatement', $query);
        $this->assertInstanceOf('\Goteo\Library\Cacher', $query->cache);
        $this->assertFalse(DB::cache(false));
        $this->assertEquals(SQL_CACHE_TIME, $query->cache->getCacheTime());
        $this->assertTrue(DB::cache(true));
        $query = $mock::query("SELECT 1");
        $this->assertInstanceOf('\PDOStatement', $query);
        $this->assertInstanceOf('\Goteo\Library\Cacher', $query->cache);
        $this->assertFalse(DB::cache(false));
        return $mock;
    }

    /**
     * @depends testInstanceQuery
     */
    public function testFailQuery($mock) {
        try {
            $mock::query('STUPID QUERY');
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        return $mock;
    }
    /**
     * @depends testFailQuery
     */
    public function testCreateTable($mock) {
        $sql =  "CREATE TEMPORARY TABLE " . $mock->getTable() . "
                ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `uniq` VARCHAR(50),
                  `name` VARCHAR(100),
                  PRIMARY KEY (`id`),
                  UNIQUE INDEX (`uniq`) )";
        // echo $sql;
        $query = $mock::query($sql);
        return $mock;
    }

    /**
     * @depends testCreateTable
     */
    public function testDbInsert($mock) {
        $tb = $mock->getTable();
        $query = $mock::query("INSERT INTO $tb (uniq, name) VALUES ('test1', 'Name 1')");
        $this->assertEquals($mock::countTotal(), 1);
        $query = $mock::query("SELECT * FROM $tb");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 1', $res->name);
        try {
            $mock::query("INSERT INTO $tb (uniq, name) VALUES ('test1', 'Name 1')");
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        $mock::query("TRUNCATE TABLE $tb");
        $this->assertEquals($mock::countTotal(), 0);

        $mock->uniq = 'test1';
        $mock->name = 'Name 2';
        $this->assertNotEmpty($mock->insert(['uniq', 'name']));
        $mock->id = $mock::insertId();
        $this->assertEquals(1, $mock->id);
        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 2', $res->name);
        $this->assertEquals(1, $res->id);
        try {
            $mock->insert(['uniq', 'name']);
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        return $mock;
    }

    /**
     * @depends testDbInsert
     */
    public function testDbUpdate($mock) {
        $tb = $mock->getTable();
        $mock->uniq = 'test2';
        $mock->name = 'Name 3';
        $mock->update(['uniq', 'name']);
        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test2', $res->uniq);
        $this->assertEquals('Name 3', $res->name);
        $this->assertEquals(1, $res->id);
        $mock->name = 'Name 4';
        $mock->update(['name'], ['uniq']);
        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test2', $res->uniq);
        $this->assertEquals('Name 4', $res->name);
        $this->assertEquals(1, $res->id);

        try {
            $mock->update(['non-existing']);
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        return $mock;
    }

    /**
     * @depends testDbUpdate
     */
    public function testDbInsertUpdate($mock) {
        $tb = $mock->getTable();
        $mock::query("TRUNCATE TABLE $tb");
        $this->assertEquals($mock::countTotal(), 0);
        $mock->id = null;
        $mock->uniq = 'test1';
        $mock->name = 'Name 1';
        try {
            $mock->update(['uniq', 'name']);
        } catch(\Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        $mock->insertUpdate(['uniq', 'name']);
        $this->assertEquals(1, $mock->id);

        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 1', $res->name);
        $this->assertEquals(1, $res->id);

        $mock->name = 'Name 2';
        $mock->insertUpdate(['name'], ['uniq']);
        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 2', $res->name);
        $this->assertEquals(1, $res->id);

        try {
            $mock->uniq = 'test2';
            $mock->insertUpdate(['name']);
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }

        return $mock;
    }

    /**
     * @depends testDbInsertUpdate
     */
    public function testQueryCache($mock) {
        DB::cache(true);
        $sql = "SELECT RAND() as num";
        $query = $mock::query($sql);
        $res1 = $query->fetchColumn();
        $this->assertLessThanOrEqual(1, $res1);
        $this->assertGreaterThan(0, $res1);
        usleep(5000);

        $query = $mock::query($sql);
        $res2 = $query->fetchColumn();

        $this->assertEquals($res1, $res2);
        //wait until cache expires
        sleep(SQL_CACHE_TIME + 1);

        $query = $mock::query($sql);
        $res2 = $query->fetchColumn();
        $this->assertNotEquals($res1, $res2);
        return $mock;
    }

    /**
     * @depends testQueryCache
     */
    public function testInvalidateCache($mock) {
        $sql = "SELECT RAND() as num";
        $query = $mock::query($sql);
        $res1 = $query->fetchColumn();
        $this->assertLessThanOrEqual(1, $res1);
        $this->assertGreaterThan(0, $res1);
        usleep(5000);
        Model::cleanCache();

        $query = Model::query($sql);
        $res2 = $query->fetchColumn();

        $this->assertNotEquals($res1, $res2);
        //wait until cache expires
        return $mock;
    }

    public static function tearDownAfterClass() {
        DB::cache(false);
    }

}
