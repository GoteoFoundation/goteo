<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */


namespace Goteo\Util\MatcherProcessor\Tests;

use Goteo\TestCase;
use Goteo\Util\MatcherProcessor\DuplicateInvestMatcherProcessor;
use Goteo\Model\Matcher;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Library\Text;

class DuplicateInvestMatcherProcessorTest extends TestCase {
    private static $data = ['id' => 'matchertest', 'name' => 'Matcher test', 'processor' => 'duplicateinvest'];
    private static $user_data = [
        ['userid' => 'simulated-user-test1', 'name' => 'Test 1', 'email' => 'test1@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 100],
        ['userid' => 'simulated-user-test2', 'name' => 'Test 2', 'email' => 'test2@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 50],
        ['userid' => 'simulated-user-test3', 'name' => 'Test 3', 'email' => 'test3@goteo.org', 'password' => 'testtest', 'active' => true]
    ];

    /**
     */
    public function testCreate() {
        \Goteo\Core\DB::cache(false);
        $errors = [];
        self::$data['owner'] = get_test_user()->id;
        $matcher = new Matcher(self::$data);
        $this->assertTrue($matcher->validate());
        $this->assertTrue($matcher->save($errors), implode("\n", $errors));
        return $matcher;
    }

    /**
     * @depends testCreate
     */
    public function testAddUsers($matcher) {
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
            if(isset($user['pool'])) {

                echo "\nSetting user's pool [{$user['userid']}]";
                Matcher::query("REPLACE invest (`user`, amount, status, method, invested, charged, pool) VALUES (:user, :amount, :status, 'dummy', NOW(), NOW(), 1)", [':user' => $user['userid'], ':amount' => $user['pool'], ':status' => Invest::STATUS_TO_POOL]);

                Matcher::query("REPLACE user_pool (`user`, amount) VALUES (:user, :amount)", [':user' => $user['userid'], ':amount' => $user['pool']]);

                $this->assertEquals($user['pool'], $uob->getPool()->amount);

                $this->assertInstanceOf('\Goteo\Model\Matcher', $matcher->addUsers($uob));
            }

            $total += $user['pool'];
        }

        $this->assertEquals($total, $matcher->getTotalAmount());
        $this->assertGreaterThan(0, $matcher->getTotalAmount());
        return $matcher;
    }

    /**
     * @depends testCreate
     */
    public function testAddProjects($matcher) {
        //Creates project first
        $matcher->addProjects(get_test_project(), 'active');
        $this->assertCount(1, $matcher->getProjects());
        return $matcher;
    }


    /**
     * @depends testCreate
     */
    public function testInstance($matcher) {

        $processor = new DuplicateInvestMatcherProcessor($matcher);

        $this->assertInstanceOf('\Goteo\Util\MatcherProcessor\DuplicateInvestMatcherProcessor', $processor);

        return $processor;
    }


    /**
     * @depends testInstance
     */
    public function testId($processor) {
        $this->assertEquals('duplicateinvest', $processor::getId());
        $this->assertTrue($processor::is($processor->getMatcher()));
    }


    /**
     * @depends testInstance
     */
    public function testName($processor) {
        $this->assertEquals('Duplicate Invest', $processor::getName());
        $this->assertEquals(Text::get('matcher-duplicateinvest-rules'), $processor::getDesc());
    }

    /**
     * @depends testInstance
     */
    public function testVars($processor) {
        $defaults = [
            'max_amount_per_project' => 500,
            'max_amount_per_invest' => 100,
            'max_invests_per_user' => 1
        ];
        $matcher = $processor->getMatcher();
        $this->assertInstanceOf('\Goteo\Model\Matcher', $matcher);
        $this->assertEquals($defaults, $processor->getVars());
        // Custom vars
        $this->assertInstanceOf('\Goteo\Model\Matcher', $matcher->setVars(['max_amount_per_project' => 150]));
        $vars = $processor->getVars();
        $this->assertNotEquals($defaults, $vars);
        $this->assertEquals(150, $vars['max_amount_per_project']);
        $this->assertEquals(100, $vars['max_amount_per_invest']);
        $this->assertEquals(1, $vars['max_invests_per_user']);
        $this->assertEquals(array_keys($defaults), array_keys($processor->getVarLabels()));

        return $processor;
    }

    /**
     * @depends testVars
     */
    public function testAmount($processor) {
        $invest = new Invest([
            'user' => get_test_user()->id,
            'project' => get_test_project()->id,
            'method' => 'dummy',
            'currency' => 'EUR',
            'currency_rate' => 1,
            'status' => Invest::STATUS_CHARGED,
            'amount' => 110
        ]);
        $processor->setInvest($invest);
        $processor->setProject(get_test_project());
        $this->assertEquals(100, $processor->getAmount());
        $invest->amount = 99;
        $this->assertEquals(99, $processor->getAmount());

        // save
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $this->assertEquals(0, $processor->getAmount());
        $invest->user = self::$user_data[1]['userid'];
        $this->assertEquals(99, $processor->getAmount());

        return $processor;
    }

    /**
     * @depends testAmount
     */
    public function testUserAmounts($processor) {
        $amounts = $processor->getUserAmounts(160);
        $this->assertCount(2, $amounts);
        $this->assertEquals(100, $amounts[self::$user_data[0]['userid']]);
        $this->assertEquals(50, $amounts[self::$user_data[1]['userid']]);

        $amounts = $processor->getUserAmounts(100);
        $this->assertCount(2, $amounts);
        $this->assertEquals(67, $amounts[self::$user_data[0]['userid']]);
        $this->assertEquals(33, $amounts[self::$user_data[1]['userid']]);

        return $processor;
    }

    /**
     * @depends testAmount
     */
    public function testInvests($processor) {
        $invests = $processor->getInvests();
        $project = $processor->getProject();
        $this->assertCount(2, $invests);
        $this->assertInstanceOf('Goteo\Model\Invest', $invests[0]);
        $this->assertInstanceOf('Goteo\Model\Invest', $invests[1]);

        $this->assertEquals(66, $invests[0]->amount);
        $this->assertEquals(33, $invests[1]->amount);

        $errors = [];
        $this->assertTrue($invests[0]->save($errors), implode("\n", $errors));
        $this->assertTrue($invests[1]->save($errors), implode("\n", $errors));

        $this->assertEquals(self::$user_data[0]['userid'], $invests[0]->user);
        $this->assertEquals(self::$user_data[1]['userid'], $invests[1]->user);
        $this->assertEquals(198, Project::get($project->id)->amount);
        return $processor;
    }

    /**
     * @depends testInvests
     */
    public function testInvestsRepeat($processor) {
        $invest = new Invest([
            'user' => get_test_user()->id,
            'project' => get_test_project()->id,
            'method' => 'dummy',
            'currency' => 'EUR',
            'currency_rate' => 1,
            'status' => Invest::STATUS_CHARGED,
            'amount' => 5
        ]);
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $processor->setInvest($invest);
        $processor->setProject(get_test_project());
        $this->assertEquals(0, $processor->getAmount());

        $invest->user = self::$user_data[2]['userid'];
        $this->assertEquals(5, $processor->getAmount());
        $invest->amount = 100;
        $this->assertEquals(51, $processor->getAmount());

        return $processor;
    }

    /**
     * @depends testCreate
     */
    public function testDelete($matcher) {
        // Delete invests
        Matcher::query("DELETE FROM invest WHERE project=?", get_test_project()->id);
        // delete matcher
        $this->assertTrue($matcher->dbDelete());

        return $matcher;
    }
    public function testCleanUsers() {
        foreach(self::$user_data as $user) {
            echo "\nDeleting user [{$user['userid']}]";
            Matcher::query("DELETE FROM invest WHERE `user` = ?", $user['userid']);
            Matcher::query("DELETE FROM user_pool WHERE `user` = ?", $user['userid']);
            $user['ob']->dbDelete();
            $this->assertEquals(0, Matcher::query("SELECT COUNT(*) FROM `user` WHERE id = ?", $user['userid'])->fetchColumn(), "Unable to delete user [{$user['userid']}]. Please delete id manually");
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
