<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\MatcherProcessor;

use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Repository\MatcherRewardRepository;

/**
 * This Processor duplicates invests with some (customizable) limits
 */
class DuplicateInvestMatcherProcessor extends AbstractMatcherProcessor {
    public const MAX_AMOUNT_PER_INVEST = 'max_amount_per_invest';
    public const MATCH_FACTOR = 'match_factor';
    public const MAX_AMOUNT_PER_PROJECT = 'max_amount_per_project';
    public const MAX_INVESTS_PER_USER = 'max_invests_per_user';
    public const MATCH_REWARDS = 'match_rewards';


    protected $default_vars = [
        self::MAX_AMOUNT_PER_PROJECT => 500,
        self::MAX_AMOUNT_PER_INVEST => 100,
        self::MAX_INVESTS_PER_USER => 1,
        self::MATCH_FACTOR => 1,
        self::MATCH_REWARDS => false
    ];

    static public function getVarLabels(): array {
        return [
            self::MAX_AMOUNT_PER_PROJECT => Text::get('matcher-duplicateinvest-max_amount_per_project'),
            self::MAX_AMOUNT_PER_INVEST => Text::get('matcher-duplicateinvest-max_amount_per_invest'),
            self::MAX_INVESTS_PER_USER => Text::get('matcher-duplicateinvest-max_invests_per_user'),
            self::MATCH_FACTOR => Text::get('matcher-duplicateinvest-match-factor'),
            self::MATCH_REWARDS => Text::get('matcher-duplicateinvest-match-rewards')
        ];
    }

    static public function getDesc(): string {
        return Text::get('matcher-duplicateinvest-rules');
    }

    /**
     * Checks if this invests has to be multiplied and
     * returns the amount to be added
     */
    public function getAmount(&$error = ''): int {
        $matcher = $this->getMatcher();

        if ($matcher->matchesRewards())
            return $this->getAmountMatchingRewards($error);

        return $this->getRegularAmount($error);
    }

    public function getAmountMatchingRewards(&$error = '') {
        $invest = $this->getInvest();
        $matcher = $this->getMatcher();
        $rewards = $invest->getRewards();
        $matcherRewardsRepository = new MatcherRewardRepository();
        $amount = 0;

        foreach ($rewards as $reward) {
            if ($matcherRewardsRepository->exists($matcher, $reward)) {
                return $this->getRegularAmount($error);
            }
        }

        $error = "There is no reward to match";
        return $amount;
    }

    public function getRegularAmount(&$error = '') {
        $invest = $this->getInvest();
        $project = $this->getProject();
        $matcher = $this->getMatcher();
        $vars = $this->getVars();
        $amount = $invest->amount;

        if($amount > $vars[self::MAX_AMOUNT_PER_INVEST]) {
            $amount = $vars[self::MAX_AMOUNT_PER_INVEST];
        }

        $amount *= $vars[self::MATCH_FACTOR];

        $invested = Invest::getList(['methods' => 'pool', 'status' => Invest::$ACTIVE_STATUSES, 'projects' => $project,'users' => $matcher->getUsers()], null, 0, 0, 'money');

        // check if current invested amount is over the maxim per project allowed
        if($vars[self::MAX_AMOUNT_PER_PROJECT] && ( $invested + $amount > $vars[self::MAX_AMOUNT_PER_PROJECT]))  {
            $amount = max(0, $vars[self::MAX_AMOUNT_PER_PROJECT] - $invested);
        }
        $count = Invest::getList(['projects' => $project, 'status' => Invest::$ACTIVE_STATUSES, 'users' => $invest->user, 'types' => 'campaign'], null, 0, 0, true);
        if($count >= $vars[self::MAX_INVESTS_PER_USER]) {
            $error = 'Max invests per user reached';
            $amount = 0;
        }

        // Check if there's enough total to extract from user's pool
        if($matcher->getAvailableAmount() < $amount) {
            $error = 'Matcher funds exhausted';
            $amount = $matcher->getAvailableAmount();
        }

        return $amount;
    }

}
