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

use Goteo\Util\MatcherProcessor\AbstractMatcherProcessor;
use Goteo\Util\MatcherProcessor\MatcherProcessorException;
use Goteo\Model\Matcher;
use Goteo\Library\Text;
use Goteo\Model\Invest;

/**
 * This Processor duplicates invests with some (customizable) limits
 */
class DuplicateInvestMatcherProcessor extends AbstractMatcherProcessor {
    protected $default_vars = [
        'max_amount_per_project' => 500,
        'max_amount_per_invest' => 100,
        'max_invests_per_user' => 1
    ];

    static public function getVarLabels() {
        return [
            'max_amount_per_project' => Text::get('matcher-duplicateinvest-max_amount_per_project'),
            'max_amount_per_invest' => Text::get('matcher-duplicateinvest-max_amount_per_invest'),
            'max_invests_per_user' => Text::get('matcher-duplicateinvest-max_invests_per_user')
        ];
    }

    static public function getDesc() {
        return Text::get('matcher-duplicateinvest-rules');
    }

    /**
     * Checks if this invests has to be multiplied and
     * returns the amount to be added
     */
    public function getAmount(&$error = '') {
        $invest = $this->getInvest();
        $project = $this->getProject();
        $matcher = $this->getMatcher();
        $vars = $this->getVars();
        $amount = $invest->amount;

        if($amount > $vars['max_amount_per_invest']) {
            $amount = $vars['max_amount_per_invest'];
        }

        $invested = Invest::getList(['methods' => 'pool', 'status' => Invest::$ACTIVE_STATUSES, 'projects' => $project,'users' => $matcher->getUsers()], null, 0, 0, 'money');

        // check if current invested amount is over the maxim per project allowed
        if($invested + $amount > $vars['max_amount_per_project']) {
            $amount = max(0, $vars['max_amount_per_project'] - $invested);
        }
        $count = Invest::getList(['projects' => $project, 'status' => Invest::$ACTIVE_STATUSES, 'users' => $invest->user], null, 0, 0, 'user');

        if($count >= $vars['max_invests_per_user']) {
            $error = 'Max invests per user reached';
            $amount = 0;
        }

        // Check if there's enough total to extract from user's pool
        if($matcher->getTotalAmount() < $amount) {
            $error = 'Matcher funds exhausted';
            $amount = $matcher->getTotalAmount();
        }

        return $amount;

    }

}
