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

use Goteo\Core\DB;
use Goteo\TestCase;
use Goteo\Util\MatcherProcessor\CriteriaInvestMatcherProcessor;
use Goteo\Model\Matcher;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Invest;

class CriteriaInvestMatcherProcessorTest extends TestCase {
    private static array $data = ['id' => 'matchertest', 'name' => 'Matcher test', 'processor' => 'criteriainvest'];
    private static array $user_data = [
        ['userid' => 'simulated-user-test1', 'name' => 'Test 1', 'email' => 'test1@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 100],
        ['userid' => 'simulated-user-test2', 'name' => 'Test 2', 'email' => 'test2@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 50],
        ['userid' => 'simulated-user-test3', 'name' => 'Test 3', 'email' => 'test3@goteo.org', 'password' => 'testtest', 'active' => true]
    ];

    public function testCreate(): Matcher {
        DB::cache(false);
        $errors = [];
        self::$data['owner'] = get_test_user()->id;
        if (!$matcher = Matcher::get(self::$data['id'])) {
            $matcher = new Matcher(self::$data);
        }
        $this->assertTrue($matcher->validate());
        $this->assertTrue($matcher->save($errors), implode("\n", $errors));
        return $matcher;
    }

    /**
     * @depends testCreate
     */
    public function testAddUsers(Matcher $matcher): Matcher {
        $total = 0;
        //Creates users first
        foreach(self::$user_data as $i => $user) {
            if(!($uob = User::get($user['userid']))) {
                $uob = new User($user);
                $this->assertTrue($uob->save($errors, ['active']), print_r($errors, 1));
            }

            $this->assertInstanceOf(User::class, $uob, print_r($errors, 1));

            self::$user_data[$i]['ob'] = $uob;
            if(isset($user['pool'])) {

                Matcher::query("REPLACE invest (`user`, amount, status, method, invested, charged, pool) VALUES (:user, :amount, :status, 'dummy', NOW(), NOW(), 1)", [':user' => $user['userid'], ':amount' => $user['pool'], ':status' => Invest::STATUS_TO_POOL]);

                Matcher::query("REPLACE user_pool (`user`, amount) VALUES (:user, :amount)", [':user' => $user['userid'], ':amount' => $user['pool']]);

                $this->assertEquals($user['pool'], $uob->getPool()->amount);

                $this->assertInstanceOf(Matcher::class, $matcher->addUsers($uob));
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

    public function testCreateConfig(Matcher $matcher): Matcher {
        $conf = [];
        $matcher->processor = CriteriaInvestMatcherProcessor::NAME;
        $conf[CriteriaInvestMatcherProcessor::PERCENT_OF_DONATION] = 50;
        $conf[CriteriaInvestMatcherProcessor::DONATION_PER_PROJECT] = 50;
        $matcher->setVars($conf);

        $errors = [];
        $this->assertTrue($matcher->save($errors), print_r($errors,1));
        return $matcher;
    }

    /**
     * @depends testCreate
     */
    public function testAddProjects($matcher) {
        //Creates project first
        $project = get_test_project();
        $project->publish();
        $matcher->addProjects($project, 'active');
        $this->assertCount(1, $matcher->getProjects());
        return $matcher;
    }


    /**
     * @depends testCreateConfig
     */
    public function testInstance($matcher): CriteriaInvestMatcherProcessor
    {
        $processor = new CriteriaInvestMatcherProcessor($matcher);

        $this->assertInstanceOf(CriteriaInvestMatcherProcessor::class, $processor);

        return $processor;
    }


    /**
     * @depends testInstance
     */
    public function testId($processor) {
        $this->assertEquals('criteriainvest', $processor::getId());
        $this->assertTrue($processor::is($processor->getMatcher()));
    }


    /**
     * @depends testInstance
     */
    public function testName($processor) {
        $this->assertEquals('Criteria Invest', $processor::getName());
    }

    /**
     * @depends testInstance
     */
    public function testVars($processor) {
        $defaults = [
            CriteriaInvestMatcherProcessor::PERCENT_OF_DONATION => 50,
            CriteriaInvestMatcherProcessor::DONATION_PER_PROJECT => 50
        ];
        $matcher = $processor->getMatcher();
        $vars = $processor->getVars();
        $this->assertInstanceOf(Matcher::class, $matcher);
        $this->assertEquals($defaults, $processor->getVars());
        // Custom vars
        $vars[CriteriaInvestMatcherProcessor::DONATION_PER_PROJECT] = 100;

        $this->assertInstanceOf(Matcher::class, $matcher->setVars($vars));
        $vars = $processor->getVars();
        // $this->assertNotEquals($defaults, $vars);
        $this->assertEquals(50, $vars[CriteriaInvestMatcherProcessor::PERCENT_OF_DONATION]);
        $this->assertEquals(100, $vars[CriteriaInvestMatcherProcessor::DONATION_PER_PROJECT]);

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
            'amount' => 1
        ]);
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $project = get_test_project();
        $project->mincost = 200;
        $project->maxcost = 4000;
        $project->amount += $invest->amount;
        // $project->save();
        $processor->setProject($project);
        $processor->setInvest($invest);
        $this->assertEquals(0, $processor->getAmount());
        $invest->amount = 100;
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $project->amount += $invest->amount;
        $this->assertEquals(100, $processor->getAmount());
        $invest->amount = 150;
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $this->assertEquals(100, $processor->getAmount());

        // save
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

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
        $this->assertInstanceOf(Invest::class, $invests[0]);
        $this->assertInstanceOf(Invest::class, $invests[1]);

        $this->assertEquals(67, $invests[0]->amount);
        $this->assertEquals(33, $invests[1]->amount);

        $errors = [];
        $this->assertTrue($invests[0]->save($errors), implode("\n", $errors));
        $this->assertTrue($invests[1]->save($errors), implode("\n", $errors));

        $this->assertEquals(self::$user_data[0]['userid'], $invests[0]->user);
        $this->assertEquals(self::$user_data[1]['userid'], $invests[1]->user);
        $this->assertEquals(250, Project::get($project->id)->amount);
        return $processor;
    }

    /**
     * @depends testInvests
     */
    public function testInvestsRepeat($processor) {
        $project = get_test_project();
        $invest = new Invest([
            'user' => get_test_user()->id,
            'project' => $project->id,
            'method' => 'dummy',
            'currency' => 'EUR',
            'currency_rate' => 1,
            'status' => Invest::STATUS_CHARGED,
            'amount' => 5
        ]);
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $processor->setInvest($invest);
        $processor->setProject($project);
        $this->assertEquals(0, $processor->getAmount());

        $invest->user = self::$user_data[2]['userid'];
        $this->assertEquals(0, $processor->getAmount());

        return $processor;
    }

    /**
     * @depends testCreate
     */
    public function testCreateConfigAmount($matcher) {
        $conf = [];
        $matcher->processor = 'criteriainvest';
        $conf[CriteriaInvestMatcherProcessor::MIN_AMOUNT_PER_PROJECT] = 500;
        $conf[CriteriaInvestMatcherProcessor::DONATION_PER_PROJECT] = 100;
        $matcher->setVars($conf);

        $errors = [];
        $this->assertTrue($matcher->save($errors), print_r($errors,1));
        return $matcher;
    }


    /**
     * @depends testCreate
     */
    public function testCleanInvests($matcher): Matcher {
        // Delete invests
        Matcher::query("DELETE FROM invest WHERE project=?", get_test_project()->id);
        $this->assertEquals(0, Matcher::query("SELECT COUNT(*) FROM `invest` WHERE project = ?", get_test_project()->id)->fetchColumn());

        $errors = [];
        $this->assertTrue($matcher->save($errors), print_r($errors,1));
        return $matcher;
    }


    /**
     * @depends testCleanInvests
     */
    public function testRefillUsersPool($matcher) {
        $total = 0;
        //Creates users first
        foreach(self::$user_data as $i => $user) {
            if(!($uob = User::get($user['userid']))) {
                $uob = new User($user);
                $this->assertTrue($uob->save($errors, ['active']), print_r($errors, 1));
            }

            $this->assertInstanceOf('\Goteo\Model\User', $uob, print_r($errors, 1));

            self::$user_data[$i]['ob'] = $uob;
            if(isset($user['pool'])) {

                Matcher::query("REPLACE invest (`user`, amount, status, method, invested, charged, pool) VALUES (:user, :amount, :status, 'dummy', NOW(), NOW(), 1)", [':user' => $user['userid'], ':amount' => $user['pool'], ':status' => Invest::STATUS_TO_POOL]);

                Matcher::query("REPLACE user_pool (`user`, amount) VALUES (:user, :amount)", [':user' => $user['userid'], ':amount' => $user['pool']]);

                $this->assertEquals($user['pool'], $uob->getPool()->amount);

            }

            $total += $user['pool'];
        }

        $this->assertEquals($total, $matcher->getTotalAmount());
        $this->assertGreaterThan(0, $matcher->getTotalAmount());

    }

    /**
     * @depends testInstance
     * @depends testCreateConfigAmount
     * @depends testRefillUsersPool
     */
    public function testAmountByAmount(CriteriaInvestMatcherProcessor $processor): CriteriaInvestMatcherProcessor {
        $invest = new Invest([
            'user' => get_test_user()->id,
            'project' => get_test_project()->id,
            'method' => 'dummy',
            'currency' => 'EUR',
            'currency_rate' => 1,
            'status' => Invest::STATUS_CHARGED,
            'amount' => 100
        ]);
        $project = get_test_project();
        $project->mincost = 200;
        $project->maxcost = 4000;
        $project->amount = $invest->amount;
        // $project->save();
        $processor->setProject($project);
        $processor->setInvest($invest);
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $this->assertEquals(0, $processor->getAmount());
        $invest->amount = 1000;
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $project->amount += $invest->amount;
        $this->assertEquals(100, $processor->getAmount());

        // save
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        return $processor;
    }

    /**
     * @depends testAmountByAmount
     */
    public function testInvestsByAmount(CriteriaInvestMatcherProcessor $processor): CriteriaInvestMatcherProcessor {
        $invests = $processor->getInvests();
        $project = $processor->getProject();
        $this->assertCount(2, $invests);
        $this->assertInstanceOf(Invest::class, $invests[0]);
        $this->assertInstanceOf(Invest::class, $invests[1]);

        $this->assertEquals(67, $invests[0]->amount);
        $this->assertEquals(33, $invests[1]->amount);

        $errors = [];
        $this->assertTrue($invests[0]->save($errors), implode("\n", $errors));
        $this->assertTrue($invests[1]->save($errors), implode("\n", $errors));

        $this->assertEquals(self::$user_data[0]['userid'], $invests[0]->user);
        $this->assertEquals(self::$user_data[1]['userid'], $invests[1]->user);
        $this->assertEquals(1100, Project::get($project->id)->amount);
        return $processor;
    }

    /**
     * @depends testAmountByAmount
     */
    public function testInvestsRepeatByAmount(CriteriaInvestMatcherProcessor $processor): CriteriaInvestMatcherProcessor {

        $project = get_test_project();
        $invest = new Invest([
            'user' => get_test_user()->id,
            'project' => $project->id,
            'method' => 'dummy',
            'currency' => 'EUR',
            'currency_rate' => 1,
            'status' => Invest::STATUS_CHARGED,
            'amount' => 5
        ]);
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $processor->setInvest($invest);
        $processor->setProject($project);
        $this->assertEquals(0, $processor->getAmount());

        $invest->user = self::$user_data[2]['userid'];
        $this->assertEquals(0, $processor->getAmount());

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
            Matcher::query("DELETE FROM invest WHERE `user` = ?", $user['userid']);
            Matcher::query("DELETE FROM user_pool WHERE `user` = ?", $user['userid']);
            $user['ob']->dbDelete();
            $this->assertEquals(0, Matcher::query("SELECT COUNT(*) FROM `user` WHERE id = ?", $user['userid'])->fetchColumn(), "Unable to delete user [{$user['userid']}]. Please delete id manually");
        }
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass(): void {
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }
}
