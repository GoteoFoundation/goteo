<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Project;
use Goteo\Model\User;

class ProjectTest extends \PHPUnit_Framework_TestCase {

    private static $data = array('id' => 'test-project', 'owner' => 'test');
    private static $user_data = array('userid' => 'test', 'name' => 'Test', 'email' => 'test@goteo.org', 'password' => 'testtest', 'active' => true);

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


    // TODO: full project test
    // public function testCreate() {

    //     //Creates the user first
    //     if(!($user = User::getByEmail(self::$user_data['email']))) {
    //         echo "Creating user [test]\n";
    //         $user = new User(self::$user_data);
    //         $this->assertTrue($user->save($errors, array('active')), print_r($errors, 1));
    //         $user = User::getByEmail(self::$user_data['email']);
    //     }
    //     $this->assertInstanceOf('\Goteo\Model\User', $user, print_r($errors, 1));

    //     $ob = new Project(self::$data);
    //     $this->assertTrue($ob->validate($errors), print_r($errors, 1));
    //     $this->assertTrue($ob->create($errors), print_r($errors, 1));

    //     $ob = Project::get($ob->id);
    //     $this->assertInstanceOf('\Goteo\Model\Project', $ob);

    //     foreach(self::$data as $key => $val) {
    //         $this->assertEquals($ob->$key, $val);
    //     }

    //     $this->assertTrue($ob->delete());

    //     return $ob;
    // }

    public function testNonExisting() {
        try {
            $ob = Project::get('non-existing-project');
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Core\Error', $e);

        }
    }
}
