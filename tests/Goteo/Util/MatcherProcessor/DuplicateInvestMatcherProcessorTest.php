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

use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\User;
use Goteo\TestCase;
use Goteo\Util\MatcherProcessor\DuplicateInvestMatcherProcessor;
use Goteo\Core\DB;

class DuplicateInvestMatcherProcessorTest extends TestCase {
    private static array $data = ['id' => 'matchertest', 'name' => 'Matcher test', 'processor' => 'duplicateinvest'];
    private static array $user_data = [
        ['userid' => 'simulated-user-test1', 'name' => 'Test 1', 'email' => 'test1@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 100],
        ['userid' => 'simulated-user-test2', 'name' => 'Test 2', 'email' => 'test2@goteo.org', 'password' => 'testtest', 'active' => true, 'pool' => 50],
        ['userid' => 'simulated-user-test3', 'name' => 'Test 3', 'email' => 'test3@goteo.org', 'password' => 'testtest', 'active' => true]
    ];

    /**
     */
    public function testCreate(): Matcher
    {
        DB::cache(false);
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
    public function testAddUsers(Matcher $matcher): Matcher
    {
        $total = 0;
        //Creates users first
        foreach(self::$user_data as $i => $user) {
            $errors = [];
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
    public function testAddProjects(Matcher $matcher): Matcher
    {
        //Creates project first
        $matcher->addProjects(get_test_project(), 'active');
        $this->assertCount(1, $matcher->getProjects());
        return $matcher;
    }


    /**
     * @depends testCreate
     */
    public function testInstance(Matcher $matcher): DuplicateInvestMatcherProcessor
    {

        $processor = new DuplicateInvestMatcherProcessor($matcher);

        $this->assertInstanceOf(DuplicateInvestMatcherProcessor::class, $processor);

        return $processor;
    }


    /**
     * @depends testInstance
     */
    public function testId(DuplicateInvestMatcherProcessor $processor) {
        $this->assertEquals('duplicateinvest', $processor::getId());
        $this->assertTrue($processor::is($processor->getMatcher()));
    }


    /**
     * @depends testInstance
     */
    public function testName(DuplicateInvestMatcherProcessor $processor) {
        $this->assertEquals('Duplicate Invest', $processor::getName());
        $this->assertEquals(Text::get('matcher-duplicateinvest-rules'), $processor::getDesc());
    }

    /**
     * @depends testInstance
     */
    public function testVars(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
        $defaults = [
            DuplicateInvestMatcherProcessor::MAX_AMOUNT_PER_PROJECT => 0,
            DuplicateInvestMatcherProcessor::MAX_AMOUNT_PER_INVEST => 100,
            DuplicateInvestMatcherProcessor::MAX_INVESTS_PER_USER => 1,
            DuplicateInvestMatcherProcessor::MATCH_FACTOR => 1,
            DuplicateInvestMatcherProcessor::MATCH_REWARDS => false
        ];
        $matcher = $processor->getMatcher();
        $this->assertInstanceOf(Matcher::class, $matcher);
        $this->assertEquals($defaults, $processor->getVars());
        // Custom vars
        $this->assertInstanceOf(Matcher::class, $matcher->setVars(['max_amount_per_project' => 150]));
        $vars = $processor->getVars();
        $this->assertNotEquals($defaults, $vars);
        $this->assertEquals(150, $vars[DuplicateInvestMatcherProcessor::MAX_AMOUNT_PER_PROJECT]);
        $this->assertEquals(100, $vars[DuplicateInvestMatcherProcessor::MAX_AMOUNT_PER_INVEST]);
        $this->assertEquals(1, $vars[DuplicateInvestMatcherProcessor::MAX_INVESTS_PER_USER]);
        $this->assertEquals(1, $vars[DuplicateInvestMatcherProcessor::MATCH_FACTOR]);
        $this->assertFalse($vars[DuplicateInvestMatcherProcessor::MATCH_REWARDS]);
        $this->assertEquals(array_keys($defaults), array_keys($processor->getVarLabels()));

        return $processor;
    }

    /**
     * @depends testVars
     */
    public function testUserAmounts(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
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
     * @depends testVars
     */
    public function testMatchFactor(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
        $invest = new Invest([
            'user' => get_test_user()->id,
            'project' => get_test_project()->id,
            'method' => 'dummy',
            'currency' => 'EUR',
            'currency_rate' => 1,
            'status' => Invest::STATUS_CHARGED,
            'amount' => 75
        ]);

        $processor->setInvest($invest);
        $processor->setProject(get_test_project());
        $matcher = $processor->getMatcher();
        $vars = $matcher->getVars();
        $vars[DuplicateInvestMatcherProcessor::MATCH_FACTOR] = 2;
        $matcher->setVars($vars);
        $this->assertEquals(150, $processor->getAmount());

        $vars[DuplicateInvestMatcherProcessor::MATCH_FACTOR] = 1;
        $matcher->setVars($vars);
        $this->assertEquals(75, $processor->getAmount());

        return $processor;
    }

    /**
     * @depends testVars
     */
    public function testAmount(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
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
        $matcher = $processor->getMatcher();

        $processedAmount = $processor->getAmount();
        $this->assertEquals(100, $processedAmount);

        $invest->amount = 99;
        $processedAmount = $processor->getAmount();
        $this->assertEquals(99, $processedAmount);

        // save
        $errors = [];
        $this->assertTrue($invest->save($errors), implode("\n", $errors));

        $drop = new Invest([
            'amount'    => $processedAmount,
            'user'      => self::$user_data[0]['userid'],
            'project'   => get_test_project()->id,
            'currency'    => $invest->currency,
            'currency_rate'    => $invest->currency_rate,
            'method'    => 'pool',
            'status'    => Invest::STATUS_CHARGED,
            'anonymous' => false,
            'resign'    => true,
            'campaign'  => true,
            'drops'     => $invest->id,
            'matcher'   => $matcher->id
        ]);
        $this->assertTrue($drop->save($errors), implode("\n", $errors));
        Invest::query("UPDATE invest SET droped = :drop, `matcher`= :matcher WHERE id = :id",
                array(':id' => $invest->id, ':drop' => $drop->id, ':matcher' => $matcher->id));
        $invest->droped = $drop->id;
        $invest->matcher = $matcher->id;
        $this->assertTrue($matcher->save($errors), implode("\n", $errors));

        return $processor;
    }

    /**
     * @depends testAmount
     */
    public function testAmountReward(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
        $matcher = $processor->getMatcher();

        $reward = get_test_reward();
        $invest = $processor->getInvest();
        $invest->addReward($reward);
        $invest->amount = 25;
        $matcher->setVars([DuplicateInvestMatcherProcessor::MAX_INVESTS_PER_USER => 2]);
        $matcher->activateMatchingRewards();

        $error = '';
        $this->assertEquals(0, $processor->getAmount($error), $error);
        $this->assertEquals("There is no reward to match", $error);

        $matcher->addMatchingReward($reward);

        $this->assertEquals($invest->getAmount(), $processor->getAmount($error), $error);

        $matcher->deactivateMatchingRewards();
        return $processor;
    }

    /**
     * @depends testAmount
     */
    public function testInvests(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
        $this->assertInstanceOf(Matcher::class, $processor->getMatcher()->setVars([DuplicateInvestMatcherProcessor::MAX_INVESTS_PER_USER => 2]));

        $invest = $processor->getInvest();
        $invest->amount = 99;

        $invests = $processor->getInvests();
        $project = $processor->getProject();
        $this->assertCount(2, $invests);
        $this->assertInstanceOf(Invest::class, $invests[0]);
        $this->assertInstanceOf(Invest::class, $invests[1]);

        $this->assertEquals(1, $invests[0]->amount);
        $this->assertEquals(50, $invests[1]->amount);

        $errors = [];
        $this->assertTrue($invests[0]->save($errors), implode("\n", $errors));
        $this->assertTrue($invests[1]->save($errors), implode("\n", $errors));

        $this->assertEquals(self::$user_data[0]['userid'], $invests[0]->user);
        $this->assertEquals(self::$user_data[1]['userid'], $invests[1]->user);
        $this->assertEquals(249, Project::get($project->id)->amount);
        return $processor;
    }

    /**
     * @depends testInvests
     */
    public function testInvestsRepeat(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
        $this->assertInstanceOf(Matcher::class, $processor->getMatcher()->setVars([DuplicateInvestMatcherProcessor::MAX_INVESTS_PER_USER => 1]));

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

        $errors = "";
        $this->assertEquals(0, $processor->getAmount($errors));
        $this->assertStringContainsString("Max invests per user reached", $errors);

        return $processor;
    }

    /**
     * @depends testInvests
     */
    public function testNoFunds(DuplicateInvestMatcherProcessor $processor): DuplicateInvestMatcherProcessor
    {
        $this->assertInstanceOf(Matcher::class, $processor->getMatcher()->setVars([DuplicateInvestMatcherProcessor::MAX_INVESTS_PER_USER => 3]));

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

        $errors = "";
        $this->assertEquals(5, $processor->getAmount($errors));

        $matcher = $processor->getMatcher();
        $drop = new Invest([
            'amount'    => $invest->amount,
            'user'      => self::$user_data[0]['userid'],
            'project'   => get_test_project()->id,
            'currency'    => $invest->currency,
            'currency_rate'    => $invest->currency_rate,
            'method'    => 'pool',
            'status'    => Invest::STATUS_CHARGED,
            'anonymous' => false,
            'resign'    => true,
            'campaign'  => true,
            'drops'     => $invest->id,
            'matcher'   => $matcher->id
        ]);
        $errors = [];
        $this->assertTrue($drop->save($errors), implode("\n", $errors));
        Invest::query("UPDATE invest SET droped = :drop, `matcher`= :matcher WHERE id = :id",
            array(':id' => $invest->id, ':drop' => $drop->id, ':matcher' => $matcher->id));
        $invest->droped = $drop->id;
        $invest->matcher = $matcher->id;
        $errors = [];
        $this->assertTrue($matcher->save($errors), implode("\n", $errors));


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

        $errors = "";
        $this->assertEquals(0, $processor->getAmount($errors));

        $this->assertStringContainsString("Matcher funds exhausted", $errors);

        return $processor;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Matcher $matcher): Matcher
    {
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
        delete_test_reward();
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }
}
