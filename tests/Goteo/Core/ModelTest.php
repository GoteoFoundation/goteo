<?php

namespace Goteo\Core\Tests;

use Exception;
use Goteo\Core\Model;
use Goteo\Core\ModelEvents;
use Goteo\Core\DB;
use Goteo\Application\Config;
use Goteo\Application\App;
use PDOException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goteo\Core\Event\CreateModelEvent;
use Goteo\Core\Event\UpdateModelEvent;
use Goteo\Core\Event\DeleteModelEvent;

class MockModel extends Model {
    public $id;
    public $uniq;
    public $name;
    public $lang;
    public $description;
    public static function get ($id) {
        $sql = "SELECT * FROM mockmodel WHERE id=?";
        return static::query($sql, $id)->fetchObject(__CLASS__);
    }
    public function save (&$errors = array()) {}
    public function validate (&$errors = array()) {}
    public static function getLangFields() {
        return ['title', 'description'];
    }
}


class MockModelListener implements EventSubscriberInterface {
    public function onCreate(CreateModelEvent $event) {
        $model = $event->getModel();
        $model->name .= ' pre-insert';
    }
    public function onCreated(CreateModelEvent $event) {
        $model = $event->getModel();
        $model->name .= ' post-insert';
    }
    public function onUpdate(UpdateModelEvent $event) {
        $model = $event->getModel();
        $model->name .= ' pre-update';
    }
    public function onUpdated(UpdateModelEvent $event) {
        $model = $event->getModel();
        $model->name .= ' post-update';
    }
    public function onDelete(DeleteModelEvent $event) {
        $model = $event->getModel();
        $model->name .= ' pre-delete';
    }
    public function onDeleted(DeleteModelEvent $event) {
        $model = $event->getModel();
        $model->name .= ' post-delete';
    }
    public static function getSubscribedEvents(): array
    {
        return [
            ModelEvents::CREATE => 'onCreate',
            ModelEvents::CREATED => 'onCreated',
            ModelEvents::UPDATE => 'onUpdate',
            ModelEvents::UPDATED => 'onUpdated',
            ModelEvents::DELETE => 'onDelete',
            ModelEvents::DELETED => 'onDeleted'
        ];
    }
}

class ModelTest extends \PHPUnit\Framework\TestCase {
    public static $listener;

    public static function setUpBeforeClass(): void {
        DB::cache(false);
        self::$listener = new MockModelListener;
    }

    public function testGetTable(): MockModel
    {
        $mock = new MockModel();
        $this->assertEquals('mockmodel', $mock->getTable());
        return $mock;
    }

    /**
     * @depends testGetTable
     */
    public function testIdealiza($mock) {
        $text = "àẁèỳśẅçÇ h😱.Bṓ";
        $this->assertEquals('aweyswcc-h-bo', Model::idealiza($text));
        $this->assertEquals('aweyswcc-h-bo', $mock::idealiza($text));
        $this->assertEquals('aweyswcc-h.bo', $mock::idealiza($text, true));
        return $mock;
    }

    /**
     * @depends testGetTable
     */
    public function testInstanceQuery($mock) {
        $query = Model::query("SELECT 1 FROM node");
        $this->assertInstanceOf('\PDOStatement', $query);
        $this->assertInstanceOf('\Goteo\Library\Cacher', $query->cache);
        $this->assertFalse(DB::cache(false));

        $this->assertEquals(Config::get('db.cache.time'), $query->cache->getCacheTime());
        $this->assertTrue(DB::cache(true));
        $query = $mock::query("SELECT 1 FROM node");
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
        catch(Exception $e) {
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
        $mock::query($sql);
        $this->assertTrue(true);
        return $mock;
    }

    /**
     * @depends testCreateTable
     */
    public function testDbInsert($mock) {
        $tb = $mock->getTable();
        $mock::query("INSERT INTO $tb (uniq, name) VALUES ('test1', 'Name 1')");
        $this->assertEquals(1, $mock::dbCount());
        $query = $mock::query("SELECT * FROM $tb");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 1', $res->name);
        try {
            $mock::query("INSERT INTO $tb (uniq, name) VALUES ('test1', 'Name 1')");
        }
        catch(Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        $mock::query("TRUNCATE TABLE $tb");
        $this->assertEquals(0, $mock::dbCount());

        $mock->uniq = 'test1';
        $mock->name = 'Name 2';
        $this->assertNotEmpty($mock->dbInsert(['uniq', 'name']));
        $mock->id = $mock::insertId();
        $this->assertEquals(1, $mock->id);
        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 2', $res->name);
        $this->assertEquals(1, $res->id);
        try {
            $mock->dbInsert(['uniq', 'name']);
        }
        catch(Exception $e) {
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
        $mock->dbUpdate(['uniq', 'name']);
        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test2', $res->uniq);
        $this->assertEquals('Name 3', $res->name);
        $this->assertEquals(1, $res->id);
        $mock->name = 'Name 4';
        $mock->dbUpdate(['name'], ['uniq']);
        $res = $mock::get(1);
        $this->assertEquals('test2', $res->uniq);
        $this->assertEquals('Name 4', $res->name);
        $this->assertEquals(1, $res->id);

        try {
            $mock->dbUpdate(['non-existing']);
        }
        catch(Exception $e) {
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
        $this->assertEquals(0, $mock::dbCount());
        $mock->id = null;
        $mock->uniq = 'test1';
        $mock->name = 'Name 1';
        try {
            $mock->dbUpdate(['uniq', 'name']);
        } catch(Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        $mock->dbInsertUpdate(['uniq', 'name']);
        $this->assertEquals(1, $mock->id);

        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 1', $res->name);
        $this->assertEquals(1, $res->id);

        $mock->name = 'Name 2';
        $mock->dbInsertUpdate(['name'], ['uniq']);
        $query = $mock::query("SELECT * FROM $tb LIMIT 1");
        $res = $query->fetchObject();
        $this->assertEquals('test1', $res->uniq);
        $this->assertEquals('Name 2', $res->name);
        $this->assertEquals(1, $res->id);

        try {
            $mock->uniq = 'test2';
            $mock->dbInsertUpdate(['name']);
        }
        catch(Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }

        return $mock;
    }

    /**
     * @depends testDbInsertUpdate
     */
    public function testDbDelete($mock) {
        $this->assertNotEmpty($mock->dbDelete());
        try {
            $mock->dbDelete();
        } catch(Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        $this->assertEquals(0, $mock::dbCount());

        //test depreacted delete func
        $mock->id=null;
        $mock->dbInsertUpdate(['uniq','name']);
        $this->assertEquals(1, $mock::dbCount());
        $this->assertTrue(MockModel::delete($mock->id));
        $this->assertEquals(0, $mock::dbCount());
        // $this->assertFalse($mock->delete());

        return $mock;
    }

    /**
     * @depends testDbDelete
     */
    public function testQueryCache($mock) {
        DB::cache(true);
        $sql = "SELECT RAND() as num FROM node";
        $query = $mock::query($sql);
        $res1 = $query->fetchColumn();
        $this->assertLessThanOrEqual(1, $res1);
        $this->assertGreaterThan(0, $res1);
        usleep(5000);

        $query = $mock::query($sql);
        $res2 = $query->fetchColumn();

        $this->assertEquals($res1, $res2);
        //wait until cache expires
        sleep(Config::get('db.cache.time') + 1);

        $query = $mock::query($sql);
        $res2 = $query->fetchColumn();
        $this->assertNotEquals($res1, $res2);
        return $mock;
    }

    /**
     * @depends testQueryCache
     */
    public function testInvalidateCache($mock) {
        $sql = "SELECT RAND() as num FROM node";
        $query = $mock::query($sql);
        $res1 = $query->fetchColumn();
        $this->assertLessThanOrEqual(1, $res1);
        $this->assertGreaterThan(0, $res1);
        usleep(5000);
        Model::cleanCache();

        $query = Model::query($sql);
        $res2 = $query->fetchColumn();

        $this->assertNotEquals($res1, $res2);
        DB::cache(false);
        return $mock;
    }

    /**
     * @depends testInvalidateCache
     */
    public function testLangsSQLJoins($mock) {
        $old_sql_lang = Config::get('sql_lang');
        $old_lang = Config::get('lang');
        Config::set('sql_lang', 'es');
        Config::set('lang', 'es');
        list($fields, $joins) = $mock::getLangsSQLJoins('es');
        // echo "\n[$joins]\n";
        $this->assertStringContainsStringIgnoringCase("IF(`mockmodel`.lang='es', `mockmodel`.`title`, IFNULL(IFNULL(b.`title`,c.`title`), `mockmodel`.`title`)) AS `title`", $fields);
        $this->assertStringContainsStringIgnoringCase("IF(`mockmodel`.lang='es', `mockmodel`.`description`, IFNULL(IFNULL(b.`description`,c.`description`), `mockmodel`.`description`)) AS `description`", $fields);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` b ON `mockmodel`.id=b.id AND b.lang='es' AND b.lang!=`mockmodel`.lang", $joins);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` c ON `mockmodel`.id=c.id AND c.lang='en' AND c.lang!=`mockmodel`.lang", $joins);

        Config::set('sql_lang', 'ca');
        list($fields, $joins) = $mock::getLangsSQLJoins('es');
        $this->assertStringContainsStringIgnoringCase("IF(`mockmodel`.lang='es', `mockmodel`.`title`, IFNULL(IFNULL(b.`title`,c.`title`), `mockmodel`.`title`)) AS `title`", $fields);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` b ON `mockmodel`.id=b.id AND b.lang='es' AND b.lang!=`mockmodel`.lang", $joins);

        list($fields, $joins) = $mock::getLangsSQLJoins('de');
        $this->assertStringContainsStringIgnoringCase("IF(`mockmodel`.lang='de', `mockmodel`.`title`, IFNULL(IFNULL(b.`title`,c.`title`), `mockmodel`.`title`)) AS `title`", $fields);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` b ON `mockmodel`.id=b.id AND b.lang='de' AND b.lang!=`mockmodel`.lang", $joins);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` c ON `mockmodel`.id=c.id AND c.lang='en' AND c.lang!=`mockmodel`.lang", $joins);

        list($fields, $joins) = $mock::getLangsSQLJoins('ca');
        $this->assertStringContainsStringIgnoringCase("IF(`mockmodel`.lang='ca', `mockmodel`.`title`, IFNULL(IFNULL(b.`title`,c.`title`), `mockmodel`.`title`)) AS `title`", $fields);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` b ON `mockmodel`.id=b.id AND b.lang='ca' AND b.lang!=`mockmodel`.lang", $joins);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` c ON `mockmodel`.id=c.id AND c.lang='en' AND c.lang!=`mockmodel`.lang", $joins);

        Config::set('sql_lang', 'ca');
        list($fields, $joins) = $mock::getLangsSQLJoins('es', Config::get('sql_lang'));
        $this->assertStringContainsStringIgnoringCase("IF('ca'='es', `mockmodel`.`title`, IFNULL(IFNULL(b.`title`,c.`title`), `mockmodel`.`title`)) AS `title`", $fields);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` b ON `mockmodel`.id=b.id AND b.lang='es' AND b.lang!='ca'", $joins);

        Config::set('sql_lang', 'es');
        Config::set('lang', 'ca');
        list($fields, $joins) = $mock::getLangsSQLJoins('de', Config::get('lang'));
        $this->assertStringContainsStringIgnoringCase("IF('ca'='de', `mockmodel`.`title`, IFNULL(IFNULL(b.`title`,c.`title`), `mockmodel`.`title`)) AS `title`", $fields);
        $this->assertStringContainsStringIgnoringCase("LEFT JOIN `mockmodel_lang` b ON `mockmodel`.id=b.id AND b.lang='de' AND b.lang!='ca'", $joins);

        Config::set('sql_lang', $old_sql_lang);
        Config::set('lang', $old_lang);
    }

    /**
     */
    public function testInsertEvents() {
        App::getService('dispatcher')->addSubscriber(self::$listener);

        $mock = new MockModel(['uniq' => 'test3', 'name' => 'INSERTED']);
        $this->assertEquals('INSERTED', $mock->name);
        $this->assertNotEmpty($mock->dbInsert(['uniq', 'name']));
        $this->assertEquals('INSERTED pre-insert post-insert', $mock->name);
        $mock->id = $mock::insertId();
        $this->assertEquals(3, $mock->id);
        $mock2 = MockModel::get($mock->id);
        $this->assertEquals('INSERTED pre-insert', $mock2->name);

        return $mock2;
    }

    /**
     * @depends testInsertEvents
     */
    public function testUpdateEvents($mock) {
        $mock->name = 'UPDATED';
        $this->assertNotEmpty($mock->dbUpdate(['name']));
        $this->assertEquals('UPDATED pre-update post-update', $mock->name);
        $this->assertEquals(3, $mock->id);

        $mock2 = MockModel::get($mock->id);
        $this->assertEquals('UPDATED pre-update', $mock2->name);

        return $mock2;
    }

    /**
     * @depends testUpdateEvents
     */
    public function testDeleteEvents($mock) {
        try {
            $mock->name = 'DELETED';
            $mock->dbDelete([]);

        } catch(PDOException $e) {
            $this->assertEquals('DELETED pre-delete', $mock->name);
        }
        $mock->name = 'DELETED';
        $mock->dbDelete(['uniq']);
        $this->assertEquals('DELETED pre-delete post-delete', $mock->name);
        $this->assertFalse($mock::get($mock->id));
        App::getService('dispatcher')->removeSubscriber(self::$listener);
    }

    public static function tearDownAfterClass(): void {
        DB::cache(false);
    }

}
