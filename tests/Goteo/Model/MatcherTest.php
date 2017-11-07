<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Matcher;
use Goteo\Model\User;
use Goteo\Model\Project;

class MatcherTest extends TestCase {
    private static $data = ['id' => 'matchertest', 'name' => 'Matcher test'];
    private static $user_data = [
        ['userid' => 'simulated-user-test1', 'name' => 'Test 1', 'email' => 'test1@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 11],
        ['userid' => 'simulated-user-test2', 'name' => 'Test 2', 'email' => 'test2@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 22]
    ];
    private static $project;

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Matcher();

        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
        $ob = new Matcher(self::$data);
        $this->assertTrue($ob->validate());
        return $ob;
    }

    /**
     * @depends testValidate
     */
    public function testCreate($ob) {
        $errors = [];
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Matcher::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob);
        // print_r($ob);die;
        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->{$key}, $val);
        }

        $this->assertEquals($ob->created, date('Y-m-d'));

        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testAddUsers($ob) {
        $total = 0;
        //Creates users first
        foreach(self::$user_data as $i => $user) {
            if(!($uob = User::get($user['userid']))) {
                echo "\nCreating user [{$user[userid]}]";
                $uob = new User($user);
                $this->assertTrue($uob->save($errors, ['active']), print_r($errors, 1));
            }

            $this->assertInstanceOf('\Goteo\Model\User', $uob, print_r($errors, 1));

            self::$user_data[$i]['ob'] = $uob;
            echo "\nSetting user's pool [{$user[userid]}]";
            Matcher::query("REPLACE user_pool (`user`, amount) VALUES (:user, :amount)", [':user' => $user['userid'], ':amount' => $user['pool']]);
            $this->assertEquals($user['pool'], $uob->getPool()->amount);

            $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->addUsers($uob));

            $total += $user['pool'];
        }

        $this->assertEquals($total, $ob->getTotalAmount());
        $this->assertGreaterThan(0, $ob->getTotalAmount());
        return $ob;
    }

    /**
     * @depends testAddUsers
     */
    public function testChangeUserPool($ob) {
        $total = $ob->getTotalAmount();
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->useUserPool(self::$user_data[1]['userid'], false));
        $this->assertEquals(self::$user_data[0]['pool'], $ob->getTotalAmount());
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->useUserPool(self::$user_data[1]['ob'], true));
        $this->assertEquals($total, $ob->getTotalAmount());
        return $ob;
    }


    /**
     * @depends testCreate
     */
    public function testAddProjects($ob) {
        $total = 0;
        //Creates project first
        $pob = get_test_project();
        $this->assertInstanceOf('\Goteo\Model\Project', $pob);

        self::$project = $pob;
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->addProjects($pob, 'active'));

        $this->assertEquals(1, $ob->getTotalProjects());
        $this->assertGreaterThan(0, $ob->getTotalProjects());
        $list = Matcher::getFromProject($pob);
        $this->assertTrue(is_array($list));
        $this->assertCount(1, $list);
        $ob2 = $list[0];
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob2);
        $this->assertEquals($ob->id, $ob2->id);
        $this->assertEquals($ob->name, $ob2->name);
        $this->assertEquals($ob->getTotalAmount(), $ob2->getTotalAmount());
        $this->assertEquals($ob->getTotalProjects(), $ob2->getTotalProjects());

        return $ob;
    }

    /**
     * @depends testAddProjects
     */
    public function testChangeProjects($ob) {
        $total = $ob->getTotalProjects();
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->setProjectStatus(self::$project, 'pending'));
        $this->assertEquals(0, $ob->getTotalProjects());
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->setProjectStatus(self::$project, 'active'));
        $this->assertEquals($total, $ob->getTotalProjects());
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->setProjectStatus(self::$project, 'rejected'));
        $this->assertEquals(0, $ob->getTotalProjects());
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->setProjectStatus(self::$project, 'active'));
        $this->assertEquals($total, $ob->getTotalProjects());
        return $ob;
    }


    /**
     * @depends testAddUsers
     */
    public function testRemoveUsers($ob) {
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->removeUsers(self::$user_data[0]['userid']));
        $this->assertEquals(self::$user_data[1]['pool'], $ob->getTotalAmount());
    }

    /**
     * @depends testAddProjecs
     */
    public function testRemoveProjects($ob) {
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->removeProjects(self::$project));
        $this->assertEquals(0, $ob->getTotalProjects());
    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());

        return $ob;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting($ob) {
        $ob = Matcher::get(self::$data['id']);
        $this->assertNull($ob);
    }

    public function testCleanUsers() {
        foreach(self::$user_data as $user) {
            echo "\nDeleting user [{$user[userid]}]";
            Matcher::query("DELETE FROM user_pool WHERE `user` = ?", $user['userid']);
            $user['ob']->dbDelete();
            $this->assertEquals(0, Matcher::query("SELECT COUNT(*) FROM `user` WHERE id = ?", $user['userid'])->fetchColumn(), "Unable to delete user [{$user[userid]}]. Please delete id manually");
        }
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }
}
