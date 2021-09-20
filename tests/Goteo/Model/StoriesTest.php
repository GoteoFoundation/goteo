<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Stories;

class StoriesTest extends \PHPUnit\Framework\TestCase {

    private static $data = array('title' => 'story title', 'description' => 'Test description', 'order' => 1, 'active' => 0);

    public function testInstance() {
        \Goteo\Core\DB::cache(false);
        $ob = new Stories();

        $this->assertInstanceOf('\Goteo\Model\Stories', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    public function testCreate() {
        $errors = [];
        self::$data['node'] = get_test_node()->id;
        self::$data['project'] = get_test_project()->id;
        $ob = new Stories(self::$data);
        $this->assertTrue($ob->validate($errors), implode("\n", $errors));
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Stories::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Stories', $ob);

        foreach(self::$data as $key => $val) {
            if($key !== 'project') $this->assertEquals($ob->$key, $val, "[$key]: " . print_r($ob->$key, 1));
            // else $this->assertInstanceOf('\Goteo\Model\Project', $ob->$key, "[$key]: " . print_r($ob->$key, 1));

        }

        $this->assertTrue($ob->dbDelete());

        return $ob;
    }
    /**
     * @depends testCreate
     */
    public function testNonExisting($ob) {
        $ob = Stories::get($ob->id);
        $this->assertFalse($ob);
    }

    /**
     * Clean up
     */
    public static function tearDownAfterClass(): void {
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }
}
