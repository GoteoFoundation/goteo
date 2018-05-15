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
use Goteo\Model\Matcher;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class MatcherTest extends TestCase {
    private static $data = ['id' => 'matchertest', 'name' => 'Matcher test', 'terms' => 'Terms test'];
    private static $trans_data = ['name' => 'Test de Matcher', 'terms' => 'Test de termes i condicions'];
    private static $user_data = [
        ['userid' => 'simulated-user-test1', 'name' => 'Test 1', 'email' => 'test1@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 11],
        ['userid' => 'simulated-user-test2', 'name' => 'Test 2', 'email' => 'test2@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 22]
    ];
    private static $project;

    public static function setUpBeforeClass() {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

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
        $this->assertFalse($ob->save());
        self::$data['owner'] = get_test_user()->id;
        $ob = new Matcher(self::$data);
        $this->assertTrue($ob->validate());
        return $ob;
    }

    /**
     * @depends testValidate
     */
    public function testCreate($ob) {
        $errors = [];
        $ob->active = false;
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Matcher::get($ob->id);
        $this->assertNull($ob);
        $ob = Matcher::get(self::$data['id'], false);
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob);

        $this->assertEquals(0, Matcher::getList(['owner' => get_test_user()->id, 'active' => true],0,0,true));
        $this->assertEquals(1, Matcher::getList(['owner' => get_test_user()->id],0,0,true));
        $this->assertEquals(1, Matcher::getList(['owner' => get_test_user()->id, 'active' => false],0,0,true));
        $this->assertCount(0, Matcher::getList(['owner' => get_test_user()->id],0,0));
        $this->assertCount(1, Matcher::getList(['name' => '%test', 'owner' => get_test_user()->id]));
        $list = Matcher::getList([],0,1);
        $this->assertCount(1, $list);
        $this->assertInstanceOf('\Goteo\Model\Matcher', $list[0]);
        $ob->active = true;
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Matcher::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob);
        // print_r($ob);die;
        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->{$key}, $val);
        }

        $this->assertEquals($ob->created, date('Y-m-d'));
        $this->assertEquals(get_test_user(), $ob->getOwner());
        $this->assertTrue($ob->userCanView(get_test_user()));
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testVars($ob) {
        $vars = ['var1' => 'Var 1', 'var2' => 'Var 2'];
        $this->assertTrue($ob->setVars($vars)->save());
        $vars = $ob->getVars();
        $this->assertCount(2, $vars);
        $this->assertEquals('Var 1', $vars['var1']);
        $this->assertEquals('Var 2', $vars['var2']);
        $ob2 = Matcher::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob2);
        $vars = $ob2->getVars();
        $this->assertCount(2, $vars);
        $this->assertEquals('Var 1', $vars['var1']);
        $this->assertEquals('Var 2', $vars['var2']);
        // $ob2 = Matcher
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
                echo "\nCreating user [{$user['userid']}]";
                $uob = new User($user);
                $this->assertTrue($uob->save($errors, ['active']), print_r($errors, 1));
            }

            $this->assertInstanceOf('\Goteo\Model\User', $uob, print_r($errors, 1));

            self::$user_data[$i]['ob'] = $uob;
            echo "\nSetting user's pool [{$user['userid']}]";
            Matcher::query("REPLACE invest (`user`, amount, status, method, invested, charged, pool) VALUES (:user, :amount, :status, 'dummy', NOW(), NOW(), 1)", [':user' => $user['userid'], ':amount' => $user['pool'], ':status' => Invest::STATUS_TO_POOL]);
            Matcher::query("REPLACE user_pool (`user`, amount) VALUES (:user, :amount)", [':user' => $user['userid'], ':amount' => $user['pool']]);
            $this->assertEquals($user['pool'], $uob->getPool()->amount);

            $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->addUsers($uob));

            $total += $user['pool'];
        }

        $this->assertEquals($total, $ob->getTotalAmount());
        $this->assertGreaterThan(0, $ob->getTotalAmount());
        $this->assertCount(2, $ob->getUsers());
        return $ob;
    }

    /**
     * @depends testAddUsers
     */
    public function testChangeUserPool($ob) {
        $total = $ob->getTotalAmount();
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->useUserPool(self::$user_data[1]['userid'], false));
        $this->assertEquals(self::$user_data[0]['pool'], $ob->getTotalAmount());

        $pools = $ob->getUserPools();
        $this->assertCount(1, $pools);
        $this->assertInstanceOf('\Goteo\Model\User\Pool', $pools[0]);
        $this->assertEquals(self::$user_data[0]['userid'], $pools[0]->user);
        $this->assertEquals(self::$user_data[0]['pool'], $pools[0]->amount);

        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->useUserPool(self::$user_data[1]['ob'], true));
        $this->assertEquals($total, $ob->getTotalAmount());
        $pools = $ob->getUserPools();
        $this->assertCount(2, $pools);
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
        $ob->active = false;
        $ob->save();
        $list = Matcher::getFromProject($pob);
        $this->assertTrue(is_array($list));
        $this->assertCount(0, $list);
        $list = Matcher::getFromProject($pob, false);
        $this->assertCount(1, $list);
        $ob->active = true;
        $ob->save();
        $list = Matcher::getFromProject($pob);
        $this->assertCount(1, $list);
        $ob2 = $list[0];
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob2);
        $this->assertEquals($ob->id, $ob2->id);
        $this->assertEquals($ob->name, $ob2->name);
        $this->assertEquals($ob->terms, $ob2->terms);
        $this->assertEquals($ob->getTotalAmount(), $ob2->getTotalAmount());
        $this->assertEquals($ob->getTotalProjects(), $ob2->getTotalProjects());

        $this->assertCount(1, $ob->getProjects());
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
     * @depends testAddProjects
     */
    public function testRemoveProjects($ob) {
        $this->assertInstanceOf('\Goteo\Model\Matcher', $ob->removeProjects(self::$project));
        $this->assertEquals(0, $ob->getTotalProjects());
    }

    /**
     * @depends testCreate
     */
    public function testSaveLanguages($ob) {
        $errors = [];
        $this->assertTrue($ob->setLang('ca', self::$trans_data, $errors), print_r($errors, 1));
        return $ob;
    }

    /**
     * @depends testSaveLanguages
     */
    public function testCheckLanguages($ob) {
        $new = Matcher::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\Matcher', $new);
        $this->assertEquals(self::$data['title'], $new->title);
        $this->assertEquals(self::$data['terms'], $new->terms);
        Lang::set('ca');
        $new2 = Matcher::get($ob->id, false, 'ca');
        $this->assertEquals(self::$trans_data['title'], $new2->title);
        $this->assertEquals(self::$trans_data['terms'], $new2->terms);
        Lang::set('es');
    }

    /**
     * @depends testCreate
     */
    public function testListing($ob) {
        $list = Matcher::getList();
        $this->assertInternalType('array', $list);
        $new = end($list);
        $this->assertInstanceOf('Goteo\Model\Matcher', $new);
        $this->assertEquals(self::$data['title'], $new->title);
        $this->assertEquals(self::$data['terms'], $new->terms);

        Lang::set('ca');
        $list = Matcher::getList();
        $this->assertInternalType('array', $list);
        $new2 = end($list);
        $this->assertEquals(self::$trans_data['title'], $new2->title);
        $this->assertEquals(self::$trans_data['terms'], $new2->terms);

        Lang::set('es');

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
            echo "\nDeleting user [{$user['userid']}]";
            Matcher::query("DELETE FROM user_pool WHERE `user` = ?", $user['userid']);
            Matcher::query("DELETE FROM invest WHERE `user` = ?", $user['userid']);
            $user['ob']->dbDelete();
            $this->assertEquals(0, Matcher::query("SELECT COUNT(*) FROM `user` WHERE id = ?", $user['userid'])->fetchColumn(), "Unable to delete user [{$user['userid']}]. Please delete id manually");
        }
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        Matcher::query("DELETE FROM matcher WHERE `id` = ?", self::$data['id']);
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }
}
