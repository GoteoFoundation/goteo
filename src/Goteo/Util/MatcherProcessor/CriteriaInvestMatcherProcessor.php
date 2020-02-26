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
use Goteo\Model\Matcher\MatcherConfig;

/**
 * This Processor duplicates invests with some (customizable) limits
 */
class CriteriaInvestMatcherProcessor extends AbstractMatcherProcessor {
    protected $default_vars = [
        'max_invests_per_user' => 1
    ];

    /**
     * Checks if this invests has to be matched and
     * returns the amount to be added
     */
    public function getAmount(&$error = '') {
        $invest = $this->getInvest();
        $project = $this->getProject();
        $matcher = $this->getMatcher();
        $vars = $this->getVars();
        $config = MatcherConfig::get($matcher->id);
        $amount = $invest->amount;
        $invested = Invest::getList(['methods' => 'pool', 'status' => Invest::$ACTIVE_STATUSES, 'projects' => $project,'users' => $matcher->getUsers()], null, 0, 0, 'money');
        if ($project->getAmountPercent() < $config->percent_of_donation || $invested) {
            $amount = 0;
        } else {
            $amount = $config->donation_per_project;
        }
        // check if current invested amount is over the maxim per project allowed
        $count = Invest::getList(['projects' => $project, 'status' => Invest::$ACTIVE_STATUSES, 'users' => $invest->user], null, 0, 0, true);

        if($vars['max_invests_per_user'] &&$count > $vars['max_invests_per_user']) {
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
