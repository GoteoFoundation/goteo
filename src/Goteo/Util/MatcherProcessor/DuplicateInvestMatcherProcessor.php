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
            'max_amount_per_project' => 'Maximum total amount to be multiplied per project',
            'max_amount_per_invest' => 'Maximum multiply per invest',
            'max_invests_per_user' => 'Maximum number of invests per user in the project'
        ];
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

        $invested = Invest::getList(['methods' => 'pool', 'status' => [Invest::STATUS_PROCESSING, Invest::STATUS_PENDING, Invest::STATUS_CHARGED, Invest::STATUS_PAID], 'projects' => $project,'users' => $matcher->getUsers()], null, 0, 0, 'money');

        // check if current invested amount is over the maxim per project allowed
        if($invested + $amount > $vars['max_amount_per_project']) {
            $amount = max(0, $vars['max_amount_per_project'] - $invested);
        }
        $count = Invest::getList(['projects' => $project, 'status' => [Invest::STATUS_PROCESSING, Invest::STATUS_PENDING, Invest::STATUS_CHARGED, Invest::STATUS_PAID], 'users' => $invest->user], null, 0, 0, 'user');

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
