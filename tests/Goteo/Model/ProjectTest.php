<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('id' => 'test-project', 'owner' => 'test');

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Project();

        $this->assertInstanceOf('\Goteo\Model\Project', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }
/*
    public function testCreate() {
        $ob = new Project(self::$data);
        $this->assertTrue($ob->validate($errors), print_r($errors, 1));
        $this->assertTrue($ob->create());

        $ob = Project::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Project', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }

        $this->assertTrue($ob->delete());

        return $ob;
    }
    */
    public function testNonExisting() {
        try {
            $ob = Project::get('non-existing-project');
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Core\Error', $e);

        }
    }
}
